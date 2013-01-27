<?php
/**
 * FileLogTest
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class FileLogTest extends \PHPUnit_Framework_TestCase {

	/**
	 * testWrite
	 *
	 * @return void
	 */
	public function testWrite() {
		$Log = new \Payment\Log\FileLog();
		$Log->write('test', 'some-test-log');

		$basedir = substr(dirname(__FILE__), 0, -17) . 'log' . DIRECTORY_SEPARATOR;
		$testLogFile = $basedir . 'some-test-log.log';

		$this->assertTrue(file_exists($testLogFile));

		$content = file_get_contents($testLogFile);
		$content = explode("\n", $content);
		$content[1] = 'test';
		unlink($testLogFile);
	}

}