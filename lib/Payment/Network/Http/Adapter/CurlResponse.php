<?php
/**
 *
 */
class CurlResponse extends  Payment\Network\Http\Response {

	protected function _parseResponse($response) {
		curl_exec($response);
	}

}