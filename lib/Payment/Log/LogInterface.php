<?php
namespace Payment\Log;
/**
 * LogInterface
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */

interface LogInterface {

/**
 * Write message to log
 *
 * @param string $message
 * @param string $type
 */
	public function write($message, $type = 'debug');

}