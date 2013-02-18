<?php
namespace Payment;
/**
 * CreditCardPaymentInterface
 *
 * @author Florian Kramer
 * @copyright 2013 Florian Kramer
 * @license MIT
 */
interface CreditCardPaymentInterface {

	/**
	 * Authorize
	 *
	 */
	public function authorize();

	/**
	 * Capture
	 *
	 */
	public function capture();

	/**
	 * Void
	 *
	 * @param mixed $transactionId
	 */
	public function void($transactionId);

	/**
	 * echeck
	 *
	 * @param mixed $transactionId
	 */
	public function echeck($transactionId);

}