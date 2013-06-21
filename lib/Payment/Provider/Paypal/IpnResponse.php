<?php
namespace Payment\Provider\Paypal;

use Payment\PaymentStatus;

/**
 * AuthorizeNet Payment Processor
 *
 * This processor class is mostly a wrapper around the official Authorize.net
 * SDK, see http://developer.authorize.net
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2013
 * @license MIT
 */
class IpnResponse extends ApiResponse {

	/**
	 * Parses the paypal response
	 */
	protected function _parseResponse() {
		if (!$this->assumeIpn()) {
			return false;
		}

		if (!$this->isValidNotification($_POST)) {

		}

		$this->_rawResponse = $_POST;

		if ($_POST['payment_status'] === 'Completed') {
			$this->_status = PaymentStatus::ACCEPTED;
		}

		$this->_transactionId = $_POST['txn_id'];
	}

	/**
	 * Basic check if the post contains paypal information
	 *
	 * This is *no* safe check, we *assume* the post is a paypal IPN if it has
	 * certain fields. You must call isValidNotification() to verify the IPN!
	 *
	 * @return boolean
	 */
	public function assumeIpn() {
		if (!empty($post) && isset($_POST['txn_id'])) {
			return true;
		}
		return false;
	}

	/**
	 *
	 */
	public function isValidNotification($data) {
		$data['cmd'] = '_notify-validate';
		$endPoint = $this->getEndpoint();
		$response = $this->_getConnection()->post($endPoint, $data);

		if (!$response->isOk()) {
			new \Payment\Exception\PaymentApiException('Error Communicating with Paypal');
		}

		if ((string)$response === 'VERIFIED') {
			return true;
		}

		return false;
	}

}

/*
 * 2013-06-13 23:20:07:
 array (
   'mc_handling1' => '1.67',
   'address_state' => 'CA',
   'txn_id' => '525025048',
   'last_name' => 'Smith',
   'mc_currency' => 'USD',
   'payer_status' => 'unverified',
   'address_status' => 'confirmed',
   'tax' => '2.02',
   'invoice' => 'abc1234',
   'address_street' => '123, any street',
   'payer_email' => 'buyer@paypalsandbox.com',
   'mc_gross1' => '9.34',
   'mc_shipping' => '3.02',
   'first_name' => 'John',
   'business' => 'seller@paypalsandbox.com',
   'verify_sign' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AfSH22aShHCMEcGKLN-hwGZy41yf',
   'payer_id' => 'TESTBUYERID01',
   'payment_date' => '16:18:42 13 Jun 2013 PDT',
   'address_country' => 'United States',
   'payment_status' => 'Completed',
   'receiver_email' => 'seller@paypalsandbox.com',
   'payment_type' => 'instant',
   'address_zip' => '95131',
   'address_city' => 'San Jose',
   'mc_shipping1' => '1.02',
   'item_name1' => 'something',
   'mc_gross' => '12.34',
   'item_number1' => 'AK-1234',
   'mc_fee' => '0.44',
   'residence_country' => 'US',
   'address_country_code' => 'US',
   'notify_version' => '2.4',
   'receiver_id' => 'seller@paypalsandbox.com',
   'mc_handling' => '2.06',
   'txn_type' => 'cart',
   'custom' => 'xyz123',
   'address_name' => 'John Smith',
   'test_ipn' => '1',
 )

 2013-06-13 23:20:24:
 array (
   'mc_handling1' => '1.67',
   'address_state' => 'CA',
   'reason_code' => 'refund',
   'txn_id' => '314623743',
   'last_name' => 'Smith',
   'mc_currency' => 'USD',
   'payer_status' => 'verified',
   'address_status' => 'confirmed',
   'tax' => '2.02',
   'invoice' => 'abc1234',
   'address_street' => '123, any street',
   'payer_email' => 'buyer@paypalsandbox.com',
   'mc_gross1' => '12.34',
   'mc_shipping' => '3.02',
   'first_name' => 'John',
   'business' => 'seller@paypalsandbox.com',
   'verify_sign' => 'AZQJzQtLerZ0KA4g1zlWsa4wdFfOA0KEYhgCMNZroolMkRJ-ykqK8Xs7',
   'parent_txn_id' => 'EARLIERTRANSID001',
   'payer_id' => 'TESTBUYERID01',
   'payment_date' => '16:19:34 13 Jun 2013 PDT',
   'address_country' => 'United States',
   'payment_status' => 'Refunded',
   'receiver_email' => 'seller@paypalsandbox.com',
   'payment_type' => 'instant',
   'address_zip' => '95131',
   'address_city' => 'San Jose',
   'mc_shipping1' => '1.02',
   'item_name1' => 'something',
   'mc_gross' => '15.34',
   'item_number1' => 'AK-1234',
   'mc_fee' => '0.44',
   'residence_country' => 'US',
   'address_country_code' => 'US',
   'notify_version' => '2.4',
   'receiver_id' => 'seller@paypalsandbox.com',
   'mc_handling' => '2.06',
   'txn_type' => 'web_accept',
   'custom' => 'xyz123',
   'address_name' => 'John Smith',
   'test_ipn' => '1',
 )


 */