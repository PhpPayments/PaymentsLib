<?php
namespace Payment;
use \Payment\Exception\MissingFieldException;
use \Payment\Log\Log;

/**
 * PaymentProcessor
 *
 * Every payment processor must extend this base class. It provides all the
 * essential functionality of the idea behind this generic payment interface.
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
abstract class PaymentProcessor {

	/**
	 * Configuration settings for this processor
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * Callback Url
	 *
	 * @var string callback url
	 */
	public $callbackUrl = '';

	/**
	 * Return Url
	 *
	 * @var string callback url
	 */
	public $returnUrl = '';

	/**
	 * Cancel Url
	 *
	 * @var string callback url
	 */
	public $cancelUrl = '';

	/**
	 * Finishing page url to display a thank you page or something like that
	 *
	 * @var string callback url
	 */
	public $finishUrl = '';

	/**
	 * Values to be used by the API implementation
	 *
	 * Structure of the array is:
	 * MethodName/VariableName/OptionsArray
	 *
	 * @var array
	 */
	protected $_fieldValidation = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float')
			),
		),
		'refund' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float')
			),
		),
	);

	/**
	 *
	 */
	protected $_fields = array();

	/**
	 * Sandbox mode
	 *
	 * Used for check if a processor is in sandbox / testing mode or not, this
	 * is important for a lot of processor to toggle between live and sandbox
	 * API callbacks and URLs
	 *
	 * @var mixed boolean
	 */
	protected $_sandboxMode = false;

	/**
	 * List of required configuration fields
	 *
	 * Every field listed in here must be present in the configuration array
	 * if not present an MissingConfigException will be thrown
	 *
	 * @var array
	 */
	protected $_configFields = array();

	/**
	 * Log object instance
	 *
	 * If false no logging will be done at all.
	 *
	 * @var LogInterface|boolean
	 */
	protected $_log = false;

	/**
	 * Http Client
	 *
	 * @var HttpClient
	 */
	protected $_HttpClient = null;

	/**
	 * Internal Payment API Version
	 *
	 * Can be used for checks to keep a processor compatible to different versions
	 *
	 * @var string
	 */
	private $__apiVersion = '1.0';

	/**
	 * Constructor
	 *
	 * @param array PaymentProcessorConfig $config
	 * @param array $options
	 * @throws Exception\PaymentProcessorException
	 * @return \Payment\PaymentProcessor
	 */
	public function __construct($config, array $options = array()) {
		if (!$this->_configure($config)) {
			throw new \Payment\Exception\PaymentProcessorException(sprintf('Failed to configure %s!', get_class($this)));
		}

		if (!$this->_initialize($options)) {
			throw new \Payment\Exception\PaymentProcessorException(sprintf('Failed to initialize %s!', get_class($this)));
		}

		$this->_initializeLogging($options);
	}

	/**
	 * Sets and gets the sandbox mode
	 *
	 * @param mixed boolean|null $sandboxMode
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function sandboxMode($sandboxMode = null) {
		if (is_null($sandboxMode)) {
			return $this->_sandboxMode;
		}

		if (!is_bool($sandboxMode)) {
			throw new \InvalidArgumentException('The first argument of that method must be null or boolean!');
		}

		if ($sandboxMode === true) {
			$this->_sandboxMode = true;
		} else {
			$this->_sandboxMode = false;
		}

		return $this->_sandboxMode;
	}

	/**
	 * Validates that all required configuration fields are present
	 *
	 * @param array $configData
	 * @throws \Payment\Exception\MissingConfigException
	 * @return void
	 */
	protected function _validateConfig($configData) {
		$passedFields = array_keys($configData);

		foreach ($this->_configFields as $requiredField) {
			if (!in_array($requiredField, $passedFields)) {
				throw new \Payment\Exception\MissingConfigException(sprintf('Missing configuration value for %s!', $requiredField));
			}
		}
	}

	/**
	 * Returns the Payments API version
	 *
	 * Use the return value of this method to compare versions to support more than
	 * one version of the payments library if you want within the same processor
	 */
	final protected function _version() {
		return $this->__apiVersion;
	}

	/**
	 * Empties the fields
	 *
	 * @return void
	 */
	public function flushFields() {
		$this->_fields = array();
	}

	/**
	 * Sets data for API calls
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return void
	 */
	public function set($field, $value = null) {
		if (is_array($field)) {
			$this->_fields = array_merge($this->_fields, $field);
			return;
		}

		$this->_fields[$field] = $value;
	}

	/**
	 * Unset a field
	 *
	 * @param string $field
	 * @return void
	 */
	public function unsetField($field) {
		unset($this->_fields[$field]);
	}

	/**
	 * Gets a field value from the set fields
	 *
	 * @param string $field
	 * @param array $options
	 * @throws \Payment\Exception\MissingFieldException
	 * @return mixed
	 */
	public function field($field, $options = array()) {
		$defaultOptions = array(
			'required' => false);

		$options = array_merge($defaultOptions, $options);

		if (!isset($this->_fields[$field])) {
			if ($options['required'] === true) {
				throw new \Payment\Exception\MissingFieldException(sprintf('Required value %s is not set!', $field));
			}

			if (isset($options['default'])) {
				return $options['default'];
			}

			throw new \Payment\Exception\MissingFieldException(sprintf('If the field %s is not set a default value must be specified!', $field));
		}

		return $this->_fields[$field];
	}

	/**
	 * Validates if all (required) values are set for an API call
	 *
	 * You really should validate if all values are set before you do anything in
	 * one of your methods to avoid the need to do a lot of manual checks on the
	 * set data and to ensure that your API call is going to get all required values
	 *
	 * @param string $action
	 * @throws \Payment\Exception\PaymentProcessorException
	 * @return boolean
	 */
	public function validateFields($action) {
		if (isset($this->_fieldValidation[$action])) {
			foreach($this->_fieldValidation[$action] as $field => $options) {
				if (isset($options['required']) && $options['required'] === true) {
					if (!isset($this->_fields[$field])) {
						throw new \Payment\Exception\PaymentProcessorException(sprintf('Required value %s is not set!', $field));
					}
				}

				if (isset($options['type'])) {
					if (is_string($options['type'])) {
						$options['type'] = array($options['type']);
					}

					$typeFound = false;
					foreach ($options['type'] as $type) {
						if ($this->validateType($type, $this->_fields[$field])) {
							$typeFound = true;
							break;
						} else {
							if (method_exists($this, $type)) {
								$method = '_' . $type;
								return $this->{$method}($action, $this->_fields[$field]);
							}
						}
					}

					if ($typeFound === false) {
						throw new \Payment\Exception\PaymentProcessorException(sprintf('Invalid data type for value %s!', $field));
					}
				}
			}
		}

		return true;
	}

	/**
	 * Validates values against data types
	 *
	 * @param string $type
	 * @param mixed $value
	 * @return bool
	 */
	public function validateType($type, $value) {
		switch ($type) :
			case 'string':
				return is_string($value);
			case 'integer':
				return is_int($value);
			case 'float':
				return is_float($value);
			case 'array':
				return is_array($value);
			case 'object':
				return is_object($value);
		endswitch;

		return false;
	}

	/**
	 * Callback to avoid overloading the constructor if you need to inject app or processor specific changes
	 *
	 * @param array $options
	 * @throws RuntimeException
	 * @return void
	 */
	protected function _initialize(array $options) {
		$this->_sandboxMode = $this->config['sandbox'];
		$this->_HttpClient = new \Payment\Network\Http\Client($this->config['httpAdapter']);
		return true;
	}

	/**
	 * Initializes the log object
	 *
	 * @param array $options
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	protected function _initializeLogging(array $options) {
		if (isset($options['logObject'])) {
			if (!is_object($options['logObject'])) {
				throw new \InvalidArgumentException('logObject must be an object and a class implemening \Payment\Log\LogInterface!');
			}

			$class = new ReflectionClass('TheClass');
			if (!$class->implementsInterface('\Payment\Log\LogInterface')) {
				throw new \RuntimeException('The log object must implement a method write($message, $logType)!');
			}
		} else {
			$this->_log = new \Payment\Log\FileLog();
		}
	}

	/**
	 * Sets configuration data, override it as needed
	 *
	 * - sandbox
	 * - log
	 * - httpAdapter
	 * - cancelUrl
	 * - returnUrl
	 * - callbackUrl
	 * - finishUrl
	 *
	 * PaymentProcessorConfig array $config
	 * @internal param bool $merge
	 * @param array $config
	 * @return void
	 * @throws \Payment\Exception\PaymentProcessorException
	 */
	protected function _configure(array $config = array()) {
		$this->_validateConfig($config);

		if (!isset($config['sandbox'])) {
			$config['sandbox'] = false;
		} else {
			$config['sandbox'] = (bool) $config['sandbox'];
		}

		if (!isset($config['httpAdapter'])) {
			$config['httpAdapter'] = 'Curl';
		}

		if (!isset($config['log'])) {
			$config['log'] = 'File';
		}

		$urls = array('cancelUrl', 'returnUrl', 'callbackUrl', 'finishUrl');
		foreach ($urls as $url) {
			if (isset($config[$url])) {
				if (!preg_match("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", $config[$url])) {
					throw new \Payment\Exception\PaymentProcessorException(sprintf('Invalid URL "%s" for the configuration key "%s"!', $config[$url], $url));
				}
				$this->{$url} = $config[$url];
			}
		}

		$this->config = $config;
		return true;
	}

	/**
	 * Redirect - Some processors requires redirects to external sites
	 *
	 * @param string $url Url to redirect to
	 */
	public function redirect($url) {
		header('Location: ' . (string) $url);
		exit();
	}

	/**
	 * Write to the log
	 *
	 * @param string $message
	 * @param string $type
	 * @return bool|void
	 */
	public function log($message, $type = null) {
		$type = get_class($this) . '_' . $type;
		return $this->_log->write($message, $type);
	}

	/**
	* Check of the processor supports a certain interface
	*
	* @param string $interfaceName
	* @return boolean
	*/
	public function supports($interfaceName) {
		return in_array($interfaceName . 'Interface', class_implements($this));
	}

	/**
	 * Method to initialize (for processor like paypal) or send the payment directly
	 *
	 * @param float $amount
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	abstract public function pay($amount, array $options = array());

	/**
	 * This method is used to process API callbacks
	 *
	 * API callbacks are usually notifications via HTTP POST or less common GET.
	 *
	 * This method should return a PaymentApiResponse
	 *
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	abstract public function notificationCallback(array $options = array());

	/**
	 * Refunds money
	 *
	 * @param $paymentReference
	 * @param $amount
	 * @param string $comment
	 * @param array $options
	 * @return PaymentApiResponse
	 * @internal param $float
	 */
	abstract public function refund($paymentReference, $amount, $comment = '', array $options = array());

	/**
	 * Cancels a payment
	 *
	 * @param string $paymentReference
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	abstract public function cancel($paymentReference, array $options = array());

}