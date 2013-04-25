<?php
namespace Payment;
/**
 * RecurringPaymentInterface
 *
 * @author Florian Kramer
 * @copyright 2013 Florian Kramer
 * @license MIT
 */
interface RecurringPaymentInterface {

	/**
	 * Cancels a subscription
	 *
	 * @param string
	 * @param array
	 * @return \Payment\ApiResponse
	 */
	public function cancelSubscription($transactionReference, array $options = array());

	/**
	 * Creates a new subscription
	 *
	 * @param array $options
	 * @return \Payment\ApiResponse
	 */
	public function createSubscription($options = array());

	/**
	 * Updates a subscription
	 *
	 * @param string
	 * @param array
	 * @return \Payment\ApiResponse
	 */
	public function updateSubscription($transactionReference, $options = array());

}