<?php
namespace Payment\Provider\AuthorizeNet;

class AimResponse extends \Payment\ApiResponse {

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

		//print_r($this->_rawResponse->error);
		//print_r($this->_rawResponse->error_message);

		$this->_transactionId = $this->_rawResponse->transaction_id;
		$this->_response = get_object_vars($this->_rawResponse);

		$this->_status = \Payment\PaymentStatus::ACCEPTED;
	}

}