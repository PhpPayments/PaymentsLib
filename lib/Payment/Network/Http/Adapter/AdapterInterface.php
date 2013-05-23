<?php
namespace Payment\Network\Http\Adapter;

interface AdapterInterface {

	/**
	 * Request method that will trigger an http request
	 *
	 * @param \Payment\Network\Http\Adapter\Payment\Network\Http\Request|\Payment\Network\Http\Request $Request
	 * @return \Payment\Network\Http\Response
	 */
	public function request(\Payment\Network\Http\Request $Request);

}