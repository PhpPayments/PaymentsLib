<?php
namespace Payment\Provider\AuthorizeNet;

/**
 * AuthorizeNet Payment Processor
 *
 * This processor class is mostly a wrapper around the official Authorize.net
 * SDK, see http://developer.authorize.net
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2013
 * @license LGPL v3
 */
class AimProcessor extends \Payment\PaymentProcessor implements \Payment\RecurringPaymentInterface {

	public $AimApi = null;

	public $ArbApi = null;

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
			'card_number' => array(
				'required' => true,
				'type' => array('integer', 'string'),
			),
			'card_code' => array(
				'required' => true,
				'type' => array('integer', 'string'),
			),
			'card_expiration_date' => array(
				'required' => true,
				'type' => array('string'),
			),
		),
	);

	/**
	 * Required configuration fields
	 *
	 * @var array
	 */
	protected $_configFields = array(
		'loginId',
		'transactionKey',
	);

	protected function _initialize(array $options) {
		if (!parent::_initialize($options)) {
			return false;
		}
		$this->_getAimApi();
		$this->_getArbApi();
		return true;
	}

	/**
	 * Get an ARB Vendor Class Instance
	 *
	 * @return \AuthorizeNetARB
	 */
	public function _getArbApi() {
		$this->ArbApi = new \AuthorizeNetARB(
			$this->config['loginId'],
			$this->config['transactionKey']);
		$this->ArbApi->setSandbox($this->_sandboxMode);
	}

	/**
	 * Get an AIM Vendor Class Instance
	 *
	 * @return \
	 */
	public function _getAimApi() {
		$this->AimApi = new \AuthorizeNetAIM(
			$this->config['loginId'],
			$this->config['transactionKey']);
		$this->AimApi->setSandbox($this->_sandboxMode);
	}

	/**
	 * Sets and gets the sandbox mode
	 *
	 * @param mixed boolean|null
	 * @return boolean
	 */
	public function sandboxMode($sandboxMode = null) {
		parent::sandboxMode($sandboxMode);
		$this->AimApi->setSandbox($this->_sandboxMode);
		$this->ArbApi->setSandbox($this->_sandboxMode);
	}

	/**
	 * Pay action
	 *
	 * @param float $amount
	 * @param array $options
	 * @return \Payment\Provider\AuthorizeNet\AimResponse
	 */
	public function pay($amount, array $options = array()) {
		$this->set('amount', (float) $amount);
		$this->validateFields('pay');

		$result = $this->AimApi->authorizeAndCapture(
			$this->field('amount'),
			$this->field('card_number'),
			$this->field('card_expiration_date'));

		return new \Payment\Provider\AuthorizeNet\AimResponse($result);
	}

	/**
	 *
	 */
	public function createSubscription($options = array()) {
		$Subscription = new AuthorizeNet_Subscription();
		$Subscription->amount = $this->field('amount');
		$Subscription->startDate = $this->field('recurring_start_date', array('default' => date('Y-m-d')));
		$Subscription->trialAmount = $this->field('recurring_trial_amount', array('default' => 0.00));
		$Subscription->totalOccurrences = $this->field('recurring_occurrence');
		$Subscription->trialOccurrences = $this->field('recurring_trial_occurrence', array('default' => 0));
		$Subscription->intervalLength = $this->field('recurring_frequency');
		$Subscription->intervalUnit = $this->field('recurring_interval');
		$Subscription->creditCardCardCode = $this->field('card_code');
		$Subscription->creditCardCardNumber = $this->field('card_number');
		$Subscription->creditCardExpirationDate = $this->field('card_expiration_date');

		$this->ArbApi = new AuthorizeNetARB();
		$result = $this->ArbApi->createSubscription($Subscription);
	}

	/**
	 *
	 */
	public function cancelSubscription($paymentReference, array $options = array()) {
		$this->ArbApi = new AuthorizeNetARB();
		$result = $this->ArbApi->cancelSubscription($paymentReference);
	}

	/**
	 *
	 */
	public function updateSubscription($paymentReference, $options = array()) {
		$Subscription =  new AuthorizeNet_Subscription();

		$this->ArbApi = new AuthorizeNetARB();
		$result = $this->ArbApi->cancelSubscription($paymentReference);
	}

	/**
	 * This method is used to process API callbacks
	 *
	 * API callbacks are usually notifications via HTTP POST or less common GET.
	 *
	 * This method should return a payment status
	 */
	public function notificationCallback(array $options = array()) {
		// TODO: Implement notificationCallback() method.
	}

	/**
	 * Refunds money
	 *
	 * @param $paymentReference
	 * @param $amount
	 * @param string $comment
	 * @param array $options
	 * @return void
	 * @link http://www.authorize.net/support/CNP/helpfiles/Search/Transaction_Detail/Refund_a_Transaction.htm
	 * @throws \Payment\Exception\UnsupportedActionException
	 */
	public function refund($paymentReference, $amount, $comment = '', array $options = array()) {
		throw new \Payment\Exception\UnsupportedActionException('Refunds are not supported');
	}

	/**
	 * Cancels a payment
	 *
	 * @param string $paymentReference
	 * @param array $options
	 * @return mixed
	 */
	public function cancel($paymentReference, array $options = array()) {
		$result = $this->AimApi->void($paymentReference);
	}

}