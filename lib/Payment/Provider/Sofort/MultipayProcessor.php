<?php
namespace Payment\Provider\Sofort;

/**
 * Sofortüberweisung Payment Processor
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2012
 * @license LGPL v3
 */
class MultiPayProcessor extends \Payment\PaymentProcessor {

	/**
	 * SofortLib_Multipay instance
	 *
	 * @var SofortLib_Multipay
	 */
	public $Multipay = null;

	/**
	 * Values to be used by the API implementation
	 *
	 * Structure of the array is:
	 * MethodName/VariableName/OptionsArray
	 *
	 * @var array
	 */
	protected $_fields = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float', 'string')),
			'payment_reason' => array(
				'required' => true,
				'type' => array('string')
			),
		),
	);

	/**
	 * Required configuration fields
	 *
	 * @var array
	 */
	protected $_configFields = array(
		'apiKey',
		'callbackUrl',
		'cancelUrl',
		'finishUrl'
	);

	/**
	 * Configures the Multipay instance with the correct callback urls and makes it
	 * available as $this->Multipay
	 *
	 * @return void
	 */
	protected function _getMultipayInstance() {
		$this->Multipay = new \SofortLib_Multipay($this->config['apiKey']);
		$this->Multipay->setNotificationUrl($this->callbackUrl);
		$this->Multipay->setAbortUrl($this->cancelUrl);
		$this->Multipay->setSuccessUrl($this->finishUrl);
	}

	/**
	 * Method to initialize the payment
	 *
	 * @param float $amount
	 * @param array $options
	 * @throws PaymentApiException
	 * @return void
	 */
	public function pay($amount, array $options = array()) {
		$this->set('amount', (float) $amount);
		$this->validateFields('pay');

		$this->_getMultipayInstance();

		$this->Multipay->setSofortueberweisung();
		$this->Multipay->setAmount($this->field('amount'));
		$this->Multipay->setReason($this->field('payment_reason'), $this->field('payment_reason2'));
		$this->Multipay->sendRequest();

		if ($this->Multipay->isError()) {
			$this->log($this->Multipay->getErrors(), 'error');
			throw new \Payment\Exception\PaymentApiException(sprintf('An error occurred please contact the shop owner.'));
		}

		if ($this->Multipay->isWarning()) {
			$this->log($this->Multipay->getWarnings(), 'warning');
		}

		$this->_transactionId = $this->Multipay->getTransactionId();

		$this->redirect($this->Multipay->getPaymentUrl());
	}

	/**
	 * Notification callback
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function notificationCallback(array $options = array()) {
		return new \Payment\Provider\Sofort\SofortNotification();
	}

	/**
	 * Cancels a payment
	 *
	 * @param string $paymentReference
	 * @param array $options
	 * @throws \Payment\Exception\UnsupportedActionException
	 * @return mixed
	 */
	public function cancel($paymentReference, array $options = array()) {
		throw new \Payment\Exception\UnsupportedActionException('Cancel is not supported by Sofort!');
	}

	/**
	 * Refund money
	 *
	 * @param $paymentReference
	 * @param float $amount
	 * @param string $comment
	 * @param array $options
	 * @return mixed
	 */
	public function refund($paymentReference, $amount, $comment = '', array $options = array()) {
		$this->set('amount', $amount);
		$this->set('comment', $comment);
		$this->set('payment_reference', $paymentReference);
		$this->validateFields('refund');

		$Sofort = new SofortLib_Refund($this->config['apiKey']);

		$Sofort->addRefund(
			$this->_fields['payment_reference'],
			$this->_fields['amount'],
			$this->_fields['comment']);

		$Sofort->setSenderAccount(
			$this->_fields['sender_account_bic'],
			$this->_fields['sender_account_iban'],
			$this->_fields['sender_account_holder']);

		return new \Payment\Provider\Sofort\ApiResponse($Sofort->sendRequest());
	}

}