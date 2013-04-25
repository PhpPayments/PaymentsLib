<?php
namespace Payment\Network\Http;
/**
 *
 */
class Request {

/**
 * HTTP Method
 *
 * @var string
 */
	protected $_method = 'POST';

/**
 * URI
 *
 * @var string
 */
	protected $_uri = null;

/**
 * Headers
 *
 * @var array
 */
	protected $_headers = array();

/**
 * Body
 *
 * @var array
 */
	protected $_body = array();

/**
 *
 */
	public function __construct($uri) {

	}

	public function post($uri) {
		$this->_method = 'POST';
		$this->_uri = $uri;
	}

	public function get($uri) {
		$this->_method = 'GET';
		$this->_uri = $uri;
	}

	public function setHeader($key, $value) {
		$this->_headers[$key] = $value;
		return;
	}

/**
 *
 */
	public function uri($uri = null) {
		if ($uri === null) {
			return $this->_uri;
		}
		if (!is_string($uri)) {
			throw new \InvalidArgumentException('URI must be a string!');
		}
		$this->_uri = $uri;
		return $this;
	}

/**
 *
 */
	public function method($method = null) {
		if ($method === null) {
			return $this->_method;
		}
		if (!is_string($method)) {
			throw new \InvalidArgumentException('Method must be a string!');
		}
		$this->_method = $method;
		return $this;
	}

	public function __set($name) {
		throw new \InvalidArgumentException();
	}

}