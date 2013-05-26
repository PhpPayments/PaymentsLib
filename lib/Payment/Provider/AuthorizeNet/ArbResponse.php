<?php
namespace Payment\Provider\AuthorizeNet;

class ArbResponse extends \Payment\ArbResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @throws \Payment\Exception\PaymentProcessorException
	 * @return void
	 */
	protected function _parseResponse() {
		if (!is_a($this->_rawResponse, 'AuthorizeNetResponse')) {
			throw new \Payment\Exception\PaymentProcessorException('Invalid object passed to the AIM response parser!');
		}

		$this->_transactionId = $this->_rawResponse->transaction_id;
		$this->_subscriptionId = $this->_rawResponse->subscription_id;
		$this->_response = get_object_vars($this->_rawResponse);

		$this->_status = \Payments\PaymentStatus::ACCEPTED;
	}

}