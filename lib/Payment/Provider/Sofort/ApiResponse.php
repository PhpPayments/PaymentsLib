<?php
namespace Payment\Provider\Sofort;

use \Payment\PaymentStatus;

/**
 * Sofortüberweisung Payment Processor
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2012
 * @license LGPL v3
 */
class ApiResponse extends \Payment\ApiResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @return void
	 */
	protected function _parseResponse() {
		if (!is_a($this->_rawResponse, '\SofortLib')) {
			throw new \Payment\Exception\PaymentApiException('Raw result was not an instance of SofortLib');
		}

		if ($this->_rawesponse->isError()) {
			$this->_errors = $this->_rawesponse->errors;
			$this->_status = PaymentStatus::ERROR;
		}

		if ($this->_rawesponse->isWarning()) {
			$this->_errors = $this->_rawesponse->warnings;
			$this->_status = PaymentStatus::ERROR;
		}

		$this->_status = PaymentStatus::SUCCESS;
	}

}