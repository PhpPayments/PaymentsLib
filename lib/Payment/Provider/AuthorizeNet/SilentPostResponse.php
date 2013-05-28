<?php
namespace Payment\Provider\AuthorizeNet;
use Payment\PaymentStatus;

/**
 * Wraps the Silent Post response from AuthorizeNet in an object
 */
class SilentPostResponse extends \Payment\ApiResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @throws \Payment\Exception\PaymentProcessorException
	 * @return void
	 */
	protected function _parseResponse() {
		if (empty($_POST['x_trans_id'])) {
			return;
		}

		if (isset($this->_options['verifyResponse']) && $this->_options['verifyResponse'] === true) {
			$this->verifyResponse();
		}

		if (!empty($_POST['x_subscription_id'])) {
			$this->_subscriptionId = $_POST['x_subscription_id'];
		}

		if (!empty($_POST['x_trans_id'])) {
			$this->_transactionId = $_POST['x_trans_id'];
		}

		if ($_POST['x_response_code'] == 1) {
			$this->_status = PaymentStatus::ACCEPTED;
		} else {
			$this->_status = PaymentStatus::DENIED;
		}

		$this->_rawResponse = $_POST;
		$this->_response = $_POST;
	}

	/**
	 *
	 */
	public function verifyResponse() {
		$hash = md5($_POST['x_trans_id'] . $_POST['x_amount']);
		return $_POST['x_amount']['x_MD5_Hash'] === $hash;
	}

}