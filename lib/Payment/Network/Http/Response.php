<?php
namespace Payment\Network\Http;
/**
 *
 */
abstract class Response {

	/**
	 * Response headers
	 *
	 * @var array
	 */
	protected $_headers = array();

	/**
	 * Response body
	 *
	 * @var string
	 */
	protected $_body = '';

	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $_status = null;

	/**
	 * Constructor
	 *
	 * @param $response mixed Can be anything
	 * @return \Payment\Network\Http\Response
	 */
	public function __construct($response) {
		$this->_parseResponse($response);
	}

	/**
	 *
	 */
	protected abstract function _parseResponse($response);

	/**
	 * Get the response body.
	 *
	 * By passing in a $parser callable, you can get the decoded
	 * response content back.
	 *
	 * For example to get the json data as an object:
	 *
	 * `$body = $response->body('json_decode');`
	 *
	 * @param callable $parser The callback to use to decode
	 *   the response body.
	 * @return mixed The response body.
	 */
	public function body($parser = null) {
		if ($parser) {
			return $parser($this->_body);
		}
		return $this->_body;
	}

	/**
	 * Headers
	 *
	 * @return integer
	 */
	public function headers() {
		return $this->_headers;
	}

	/**
	 * Status
	 *
	 * @return integer
	 */
	public function status() {
		return (int) $this->_status;
	}

}