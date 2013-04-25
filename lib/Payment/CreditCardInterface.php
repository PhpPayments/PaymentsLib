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
	 * @return \Payment\ApiResponse
	 */
	public function authorize();

	/**
	 * Capture
	 *
	 * @return \Payment\ApiResponse
	 */
	public function capture();

	/**
	 * Void
	 *
	 * @param mixed $transactionId
	 * @return \Payment\ApiResponse
	 */
	public function void($transactionId);

	/**
	 * echeck
	 *
	 * @param mixed $transactionId
	 * @return \Payment\ApiResponse
	 */
	public function echeck($transactionId);

}