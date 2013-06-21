<?php
/**
 * TestApiResponse
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */
class TestApiResponse extends \Payment\ApiResponse {

	/**
	 * This method must parse the raw response and set the protected properties of
	 * this class based on the response
	 *
	 * @return void
	 */
	protected function _parseResponse() {
		if (empty($_POST)) {
			return;
		}

		$this->_rawResponse = $_POST;

		if (isset($_POST['status']) && $_POST['status'] === 'success') {
			$this->_status = \Payment\PaymentStatus::ACCEPTED;
		}

		$this->_status = \Payment\PaymentStatus::FAILED;
	}
}

/**
 * ApiResponseTest
 *
 * @author Florian Krämer
 * @copyright 2013 Florian Krämer
 * @license MIT
 */

class ApiResponseTest extends \PHPUnit_Framework_TestCase {

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();

	}

	/**
	 * testResponse
	 *
	 * @return void
	 */
	public function testStatus() {

	}

	/**
	 * testResponse
	 *
	 * @return void
	 */
	public function testSubscriptionId() {

	}

	/**
	 * testResponse
	 *
	 * @return void
	 */
	public function testTransactionId() {

	}

	/**
	 * testResponse
	 *
	 * @return void
	 */
	public function testResponse() {

	}

	/**
	 * testResponse
	 *
	 * @return void
	 */
	public function testRawResponse() {

	}

}