<?php
namespace Payment;
use \Payment\Exception\PaymentApiException;

/**
 * PaymentApiResponse
 *
 * This class does *not* implement any kind of transport protocol like http POST
 * or GET. The only purpose of this class to construct a standard object to work
 * with based on the response of any API and any protocol.
 *
 * If you received XML via post pass it to the constructor as string. If you
 * received XML as a file, read it and pass it as string to your constructor.
 * If you got already a SimpleXml object pass it on to the constructor.
 *
 * You must implement the _parseResponse() method that must parse the raw API
 * response and set the protected properties of this class if they're valid
 * for the API
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
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
	 * Parsed response of a payment processor, must be an array and must contain
	 * all data coming from the API
	 *
	 * @var array
	 */
	protected $_response = null;

	/**
	 * Transaction Id for processors that return one
	 *
	 * @var null|integer|string
	 */
	protected $_transactionId = null;

	/**
	 * Subscription Id for processors that implement subscriptions
	 *
	 * @var null|integer|string
	 */
	protected $_subscriptionId = null;

	/**
	 * Constructor
	 *
	 * @param string|array|object
	 * @param array $options
	 * @return \Payment\ApiResponse
	 * @throws \Payment\Exception\PaymentApiException
	 */
	public function __construct($response, $options = array()) {
		$this->_options = $options;
		$this->_rawResponse = $response;
		$this->_parseResponse();

		if (is_null($this->_response)) {
			throw new \Payment\Exception\PaymentApiException('PaymentApiResponse::_parseResponse() could not or did not parse the response!');
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
	 *
	 */
	public function errors() {
		return $this->_errors;
	}

	/**
	 * Magic getter
	 *
	 * @param string $name
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