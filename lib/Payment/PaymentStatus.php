<?php
namespace Payments;
/**
 * PaymentStatus
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class PaymentStatus {

	/**
	 * Payment has been initialized and is awaiting a notification or other status change
	 */
	const PENDING = 'pending';

	/**
	 * Cancelled by the user or system
	 */
	const CANCELLED = 'cancelled';

	/**
	 * Used when a payment was reject, for example a credit card is invalid or expired
	 */
	const FAILED = 'failed';

	/**
	 * Used when a payment settled or was accepted
	 */
	const ACCEPTED = 'accepted';

	/**
	 * Used for code or API errors
	 */
	const ERROR = 'error';

	/**
	 * Denied for example when there are restrictions on the amount of
	 * transferable money or other constraints
	 */
	const DENIED = 'denied';

	/**
	 * Refunded successfully
	 */
	const REFUNDED = 'refunded';

	/**
	 * Partially refunded
	 */
	const PARTIAL_REFUNDED = 'partial_refunded';

}