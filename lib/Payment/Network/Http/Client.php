<?php
namespace Payment\Network\Http;
/**
 * Http Client Class
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class Client {

	/**
	 * Http Adapter
	 *
	 * @mixed
	 */
	protected $_Adapter = null;

	/**
	 * Config
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct($config = array()) {
		$this->config($config);

		$adapter = null;
		if (isset($this->_config['adapter'])) {
			$adapter = $this->_config['adapter'];
		}
		$this->_loadAdapter($adapter);
	}

	/**
	 * Load a http adapter class
	 *
	 * @param string $adapter
	 * @return void
	 * @throws RuntimeException if an adapter could not be loaded
	 * @todo finish and use the stream adapter as fallback if cURL is not present
	 */
	protected function _loadAdapter($adapter = null) {
		if (empty($adapter)) {
			if (function_exists('curl_init')) {
				$this->_Adapter =  new \Payment\Network\Http\Adapter\Curl();
			} else {
				//$this->_Adapter =  new \Payment\Network\Http\Adapter\Stream();
				throw new \RuntimeException('cURL is not installed, please see http://www.php.net/manual/en/curl.installation.php');
			}
		} else {
			if (is_string($adapter)) {
				$this->_Adapter = new $adapter();
			} elseif (is_object($adapter)) {
				$this->_Adapter = $adapter;
			}
		}

		if (empty($this->_Adapter)) {
			throw new \RuntimeException('Could not load a Http Adapter!');
		}
	}

	/**
	 * Get or set additional config options.
	 *
	 * Setting config will use array_merge for appending into
	 * the existing configuration.
	 *
	 * @param array|null $config Configuration options. null to get.
	 * @return this|array
	 */
	public function config($config = null) {
		if ($config === null) {
			return $this->_config;
		}

		//$this->_config = array_merge($this->_config, $config);
		return $this;
	}

	/**
	 * Send a HTTP request
	 *
	 * @param \Payment\Network\Http\Request $Request
	 * @throws \RuntimeException if the response is not a \Payment\Network\Http\Response object
	 * @return \Payment\Network\Http\Response
	 */
	final public function send(\Payment\Network\Http\Request $Request) {
		$response = $this->_Adapter->request($Request);
		if (!is_a($response, '\Payment\Network\Http\Response')) {
			throw new \RuntimeException(sprintf('Invalid Http Adapter class %s!', get_class($response)));
		}
		return $response;
	}

}