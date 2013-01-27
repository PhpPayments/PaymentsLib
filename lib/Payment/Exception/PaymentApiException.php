<?php
namespace Payment\Exception;
/**
 * PaymentApiException
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class PaymentApiException extends Exception {

	public $apiErrorCode = null;
	public $apiErrorMessage = null;
	public $apiErrorReason = null;
}