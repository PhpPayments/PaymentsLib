<?php
namespace Payment\Provider\Sofort;

/**
 * Sofortüberweisung Payment Processor
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2012
 * @license LGPL v3
 */
class MultiPayApiResponse extends \Payment\Provider\Sofort\SofortApiResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @return void
	 */
	protected function _parseResponse() {
		return parent::_parseResponse();
	}

}