<?php
/**
 * TestPaymentProcessor
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class TestPaymentProcessor extends \Payment\PaymentProcessor {

	protected $_fieldValidation = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float')
			),
		),
		'refund' => array(
			'amount' => array(
				'required' => false,
				'type' => array('integer', 'float')
			),
		),
	);

	public function pay($amount, array $options = array()) {

	}

	public function notificationCallback(array $options = array()) {

	}

	public function refund($paymentReference, $amount, $comment = '', array $options = array()) {

	}

	public function cancel($paymentReference, array $options = array()) {

	}

}

/**
 * BasePaymentProcessorTest
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class PaymentProcessorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();

	}

	/**
	 * testField
	 *
	 * @return void
	 */
	public function testField() {
		$Processor = new \TestPaymentProcessor(array());
		$result = $Processor->field('foo', array('default' => 'bar'));
		$this->assertEquals($result, 'bar');

		$Processor->set('bar', 'foo');
		$result = $Processor->field('bar');
		$this->assertEquals($result, 'foo');
	}

	/**
	 * testValidateFields
	 *
	 * @return void
	 * @expectedException \Payment\Exception\PaymentProcessorException
	 */
	public function testValidateFieldsExceptionFieldNotSet() {
		$Processor = new \TestPaymentProcessor(array());
		$Processor->validateFields('pay');
	}

	/**
	 * testValidateFields
	 *
	 * @return void
	 * @expectedException \Payment\Exception\PaymentProcessorException
	 */
	public function testValidateFieldsExceptionInvalidFieldValue() {
		$Processor = new \TestPaymentProcessor(array());
		$Processor->set('amount', 'no string!');
		$Processor->validateFields('pay');
	}

}