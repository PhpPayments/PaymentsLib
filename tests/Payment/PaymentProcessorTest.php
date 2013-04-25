<?php
/**
 * BasePaymentProcessorTest
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class TestPaymentProcessor extends \Payment\PaymentProcessor {

	public function pay($amount, array $options = array()) {

	}

	public function notificationCallback(array $options = array()) {

	}

	public function refund($paymentReference, $amount, $comment = '', array $options = array()) {

	}

	public function cancel($paymentReference, array $options = array()) {

	}

}

class BasePaymentProcessorTest extends \PHPUnit_Framework_TestCase {

	public function testField() {
		$Processor = new \TestPaymentProcessor(array());
		$Processor->field('test');
	}

}