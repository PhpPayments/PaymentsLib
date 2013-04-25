<?php
namespace Payment\Network\Http\Adapter;
/**
 * Curl wrapper class
 *
 * @author Florian Krämer
 * @copyright 2012 Florian Krämer
 * @license MIT
 */
class CakePhp2HttpSocket {

	public function request(\Payment\Network\Http\Request $Request) {
		$this->Socket = new HttpSocket();

	}

}