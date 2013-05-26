<?php
namespace Payment\Provider\Sofort;

class MultiPayApiResponse extends \Payment\ApiResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @return void
	 */
	protected function _parseResponse() {
		$Sofort = new \SofortLib_Notification();

		if ($Sofort->isError()) {
			$this->_rawResponse = $Sofort;
			$this->log($Sofort->getErrors(), 'error');
			return false;
		}

		if ($Sofort->isWarning()) {
			$this->log($Sofort->getWarnings(), 'warning');
		}

		$this->_transactionId = $Sofort->getNotification();

		$Sofort = new \SofortLib_TransactionData($this->config['apiKey']);
		$Sofort->setTransaction($this->_transactionId)->sendRequest();
		$this->_rawResponse = $Sofort;

		$status = $Sofort->getStatus();

		if ($status == 'pending') {
			$this->_status = PaymentStatus::PENDING;
		}

		if ($status == 'received') {
			$this->_status = PaymentStatus::SUCCESS;
		}
	}

	/**
	 * Scnittestellenbeschreibung_SOFORT_Überweisung.pdf Page 26
	 *
	 * @param $status
	 * @param $reason
	 * @return array
	 */
	public function mapStatus($status, $reason) {
		$message = '';
		$paymentStatus = '';

		if ($status == 'loss' && $reason == 'complaint') {
			$message = __d('sofort', 'Der Käuferschutz wurde in Anspruch genommen.');
			$paymentStatus = PaymentStatus::FAILED;
		}

		if ($status == 'loss' && $reason == 'not_credited') {
			$message = __d('sofort', 'Das Geld ist nicht eingegangen..');
			$paymentStatus = PaymentStatus::FAILED;
		}

		if ($status == 'pending' && $reason == 'not_credited_yet') {
			$message = __d('sofort', 'Das Geld ist noch nicht eingegangen..');
			$paymentStatus = PaymentStatus::PENDING;
		}

		if ($status == 'received' && $reason == 'consumer_protection') {
			$message = __d('sofort', 'Das Geld ist auf dem Treuhandkonto eingegangen.');
			$paymentStatus = PaymentStatus::SUCCESS;
		}

		if ($status == 'received' && $reason == 'credited') {
			$message = __d('sofort', 'Das Geld ist eingegangen.');
			$paymentStatus = PaymentStatus::SUCCESS;
		}

		if ($status == 'refunded' && $reason == 'compensation') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (Teilrückbuchung).');
			$paymentStatus = PaymentStatus::PARTIAL_REFUNDED;
		}

		if ($status == 'refunded' && $reason == 'refunded') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (komplette Rückbuchung des Gesamtbetrags).');
			$paymentStatus = PaymentStatus::REFUNDED;
		}

		return compact($paymentStatus, $message);
	}

}