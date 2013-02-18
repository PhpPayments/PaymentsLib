<?php
namespace Payment\Exception;
/**
 * UnsupportedPaymentActionException
 *
 * Throw this type of exception when a processor can not implement one method because the API does not support it.
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class UnsupportedActionException extends \Payment\Exception\PaymentException {

}