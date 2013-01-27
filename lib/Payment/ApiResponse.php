<?php
namespace Payment;
/**
 * PaymentApiResponse
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
abstract class ApiResponse {

/**
 * Status, must be one of the PaymentStatus class constants
 *
 * @var string
 */
	protected $_status = null;

/**
 * Raw response of a payment processor
 *
 * @var string|array|object
 */
	protected $_rawResponse = null;

/**
 * Response of a payment processor, must be an array
 *
 * @var array
 */
	protected $_response = null;

/**
 * Transaction Id for processors that return one
 *
 * @var mixed
 */
	protected $_transactionId = null;

/**
 * Subscription Id for processors that implement subscriptions
 *
 * @var mixed
 */
	protected $_subscriptionId = null;

/**
 * Constructor
 *
 * @param string|array|object
 * @param array $options
 * @return \Payment\ApiResponse
 * @throws \RuntimeException
 */
	public function __construct($response, $options = array()) {
		$this->_options = $options;
		$this->_rawResponse = $response;
		$this->_parseResponse();

		if (is_null($this->_response)) {
			throw new \RuntimeException('PaymentApiResponse::_parseResponse() could not or did not parse the response!');
		}
	}

/**
 * getTransactionId
 *
 * @return string
 */
	public function transactionId() {
		return $this->_transactionId;
	}

/**
 * getSubscriptionId
 *
 * @return string
 */
	public function subscriptionId() {
		return $this->_subscriptionId;
	}

/**
 * Get the raw API response
 *
 * @return mixed
 */
	public function rawResponse() {
		return $this->_rawResponse;
	}

/**
 * Get the status
 *
 * @return mixed
 */
	public function status() {
		return $this->_status;
	}

/**
 * Get the raw API response
 *
 * @return mixed
 */
	public function response() {
		return $this->_response;
	}

/**
 * Magic getter
 *
 * @return mixed
 */
	public function __get($name) {
		if (isset($this->_{$name})) {
			return $this->_{$name};
		}
	}

/**
 * This method must parse the raw response and set the protected properties of
 * this class based on the response
 *
 * @return void
 */
	protected abstract function _parseResponse();

}