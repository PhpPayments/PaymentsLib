<?php
namespace Payment\Log;
/**
 * FileLog
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */

class FileLog implements LogInterface {

	/**
	 * Log file directory, absolute path
	 *
	 * @var string
	 */
	public $logDir = null;

	/**
	 * Constructor
	 *
	 * @param array $options
	 * @throws \RuntimeException
	 * @return \Payment\Log\FileLog
	 */
	public function __construct(array $options = array()) {
		if (!isset($options['logPath'])) {
			$this->logDir = substr(dirname(__FILE__), 0, -15);
			$this->logDir .= 'log' . DIRECTORY_SEPARATOR;
		} else {
			$this->logDir = $options['logPath'];
		}

		if (!is_dir($this->logDir)) {
			throw new \RuntimeException(sprintf('Log directory %s does not exist!', $this->logDir));
		}

		if (!is_writeable($this->logDir)) {
			throw new \RuntimeException(sprintf('Log directory %s is not writeable!', $this->logDir));
		}
	}

	/**
	 * Write the data to the log
	 *
	 * @param string $message
	 * @param string $type
	 * @return boolean
	 */
	public function write($message, $type = 'debug') {
		if (!is_string($message)) {
			$message = var_dump($message);
		}

		$datePrefix = date('Y-m-d H:i:s') . ': ' . "\n";
		$file = $type . '.log';
		$file = str_replace(DIRECTORY_SEPARATOR, '-', $file);

		$handle = fopen($this->logDir . $file, 'a+');
		fwrite($handle, $datePrefix);
		fwrite($handle, $message . "\n\n");
		return fclose($handle);
	}

}