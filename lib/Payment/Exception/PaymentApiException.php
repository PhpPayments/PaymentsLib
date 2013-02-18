<?php
namespace Payment\Exception;
/**
 * PaymentApiException
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class PaymentApiException extends \Payment\Exception\PaymentException {

	public $apiErrorCode = null;
	public $apiErrorMessage = null;
	public $apiErrorReason = null;
}