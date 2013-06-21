<?php
namespace Payment\Provider\Paypal;

use Payment\PaymentApiResponse;

class ExpressCheckoutProcessor extends \Payment\PaymentProcessor {

	/**
	 * Method to initialize (for processor like paypal) or send the payment directly
	 *
	 * @param float $amount
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	public function pay($amount, array $options = array())
	{
		$currencyCode = 'USD';
		// total shipping amount
		//$shippingTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, $_REQUEST['shippingTotal']);
		$shippingTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, 0);
		//total handling amount if any
		//$handlingTotal = new BasicAmountType($currencyCode, $_REQUEST['handlingTotal']);
		$handlingTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, 0);
		//total insurance amount if any
		//$insuranceTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, $_REQUEST['insuranceTotal']);
		$insuranceTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, 0);
/*
		// shipping address
		$address = new AddressType();
		$address->CityName = $_REQUEST['city'];
		$address->Name = $_REQUEST['name'];
		$address->Street1 = $_REQUEST['street'];
		$address->StateOrProvince = $_REQUEST['state'];
		$address->PostalCode = $_REQUEST['postalCode'];
		$address->Country = $_REQUEST['countryCode'];
		$address->Phone = $_REQUEST['phone'];
*/
		// details about payment
		$paymentDetails = new \PayPal\EBLBaseComponents\PaymentDetailsType();
		$itemTotalValue = 0;
		$taxTotalValue = 0;
		/*
		 * iterate trhough each item and add to atem detaisl
		 */
		/*
		for($i=0; $i<count($_REQUEST['itemAmount']); $i++) {
			$itemAmount = new BasicAmountType($currencyCode, $_REQUEST['itemAmount'][$i]);
			$itemTotalValue += $_REQUEST['itemAmount'][$i] * $_REQUEST['itemQuantity'][$i];
			$taxTotalValue += $_REQUEST['itemSalesTax'][$i] * $_REQUEST['itemQuantity'][$i];
			$itemDetails = new PaymentDetailsItemType();
			$itemDetails->Name = $_REQUEST['itemName'][$i];
			$itemDetails->Amount = $itemAmount;
			$itemDetails->Quantity = $_REQUEST['itemQuantity'][$i];
			/*
			 * Indicates whether an item is digital or physical. For digital goods, this field is required and must be set to Digital. It is one of the following values:
		    Digital
		    Physical
			 */
		/*
			$itemDetails->ItemCategory = $_REQUEST['itemCategory'][$i];
			$itemDetails->Tax = new BasicAmountType($currencyCode, $_REQUEST['itemSalesTax'][$i]);

			$paymentDetails->PaymentDetailsItem[$i] = $itemDetails;
		}
		*/

		/*
		 * The total cost of the transaction to the buyer. If shipping cost and tax charges are known, include them in this value. If not, this value should be the current subtotal of the order. If the transaction includes one or more one-time purchases, this field must be equal to the sum of the purchases. If the transaction does not include a one-time purchase such as when you set up a billing agreement for a recurring payment, set this field to 0.
		 */
		$orderTotalValue = $shippingTotal->value + $handlingTotal->value +
		$insuranceTotal->value +
		$itemTotalValue + $taxTotalValue;

		//Payment details
		//$paymentDetails->ShipToAddress = $address;
		$paymentDetails->ItemTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, $itemTotalValue);
		$paymentDetails->TaxTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, $taxTotalValue);
		$paymentDetails->OrderTotal = new \PayPal\CoreComponentTypes\BasicAmountType($currencyCode, $orderTotalValue);

		/*
		 * How you want to obtain payment. When implementing parallel payments, this field is required and must be set to Order. When implementing digital goods, this field is required and must be set to Sale. If the transaction does not include a one-time purchase, this field is ignored. It is one of the following values:

		    Sale � This is a final sale for which you are requesting payment (default).

		    Authorization � This payment is a basic authorization subject to settlement with PayPal Authorization and Capture.

		    Order � This payment is an order authorization subject to settlement with PayPal Authorization and Capture.

		 */
		//$paymentDetails->PaymentAction = $_REQUEST['paymentType'];
		$paymentDetails->PaymentAction = 'Order';
		$paymentDetails->HandlingTotal = $handlingTotal;
		$paymentDetails->InsuranceTotal = $insuranceTotal;
		$paymentDetails->ShippingTotal = $shippingTotal;

		/**
		 * Your URL for receiving Instant Payment Notification (IPN) about this transaction. If you do not specify this value in the request, the notification URL from your Merchant Profile is used, if one exists.
		 */
		if (is_string($this->callbackUrl)) {
			$paymentDetails->NotifyURL = $this->callbackUrl;
		}

		$setECReqDetails = new \PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType();
		$setECReqDetails->PaymentDetails[0] = $paymentDetails;
		/*
		 * (Required) URL to which the buyer is returned if the buyer does not approve the use of PayPal to pay you. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */
		$setECReqDetails->CancelURL = $this->cancelUrl;
		/*
		 * (Required) URL to which the buyer's browser is returned after choosing to pay with PayPal. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */
		$setECReqDetails->ReturnURL = $this->returnUrl;

		/*
		 * Determines where or not PayPal displays shipping address fields on the PayPal pages. For digital goods, this field is required, and you must set it to 1. It is one of the following values:
		    0 � PayPal displays the shipping address on the PayPal pages.
		    1 � PayPal does not display shipping address fields whatsoever.
		    2 � If you do not pass the shipping address, PayPal obtains it from the buyer's account profile.
		 */
		//$setECReqDetails->NoShipping = $_REQUEST['noShipping'];
		/*
		 *  (Optional) Determines whether or not the PayPal pages should display the shipping address set by you in this SetExpressCheckout request, not the shipping address on file with PayPal for this buyer. Displaying the PayPal street address on file does not allow the buyer to edit that address. It is one of the following values:
		    0 � The PayPal pages should not display the shipping address.
		    1 � The PayPal pages should display the shipping address.
		 */
		//$setECReqDetails->AddressOverride = $_REQUEST['addressOverride'];

		/*
		 * Indicates whether or not you require the buyer's shipping address on file with PayPal be a confirmed address. For digital goods, this field is required, and you must set it to 0. It is one of the following values:
		    0 � You do not require the buyer's shipping address be a confirmed address.
		    1 � You require the buyer's shipping address be a confirmed address.
		 */
		//$setECReqDetails->ReqConfirmShipping = $_REQUEST['reqConfirmShipping'];

		// Billing agreement details
		//$billingAgreementDetails = new \PayPal\EBLBaseComponents\BillingAgreementDetailsType($_REQUEST['billingType']);
		//$billingAgreementDetails->BillingAgreementDescription = $_REQUEST['billingAgreementText'];
		//$setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);

		// Display options
/*
		$setECReqDetails->cppheaderimage = $_REQUEST['cppheaderimage'];
		$setECReqDetails->cppheaderbordercolor = $_REQUEST['cppheaderbordercolor'];
		$setECReqDetails->cppheaderbackcolor = $_REQUEST['cppheaderbackcolor'];
		$setECReqDetails->cpppayflowcolor = $_REQUEST['cpppayflowcolor'];
		$setECReqDetails->cppcartbordercolor = $_REQUEST['cppcartbordercolor'];
		$setECReqDetails->cpplogoimage = $_REQUEST['cpplogoimage'];
		$setECReqDetails->PageStyle = $_REQUEST['pageStyle'];
		$setECReqDetails->BrandName = $_REQUEST['brandName'];
*/
		// Advanced options
		//$setECReqDetails->AllowNote = $_REQUEST['allowNote'];

		$setECReqType = new \PayPal\PayPalAPI\SetExpressCheckoutRequestType();
		$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
		$setECReq = new \PayPal\PayPalAPI\SetExpressCheckoutReq();
		$setECReq->SetExpressCheckoutRequest = $setECReqType;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		configuration file for your credentials and endpoint
		*/
		$paypalService = new \PayPal\Service\PayPalAPIInterfaceServiceService();
		try {
			/* wrap API method calls on the service object with a try catch */
			$setECResponse = $paypalService->SetExpressCheckout($setECReq);
		} catch (Exception $e) {
			throw $e;
		}
		if (isset($setECResponse)) {
			//echo "<table>";
			//echo "<tr><td>Ack :</td><td><div id='Ack'>$setECResponse->Ack</div> </td></tr>";
			//echo "<tr><td>Token :</td><td><div id='Token'>$setECResponse->Token</div> </td></tr>";
			//echo "</table>";
			//echo '<pre>';
			//print_r($setECResponse);
			//echo '</pre>';
			if ($setECResponse->Ack === 'Success') {
				$token = $setECResponse->Token;
				// Redirect to paypal.com here
				$payPalURL = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
				//echo" <a href=$payPalURL><b>* Redirect to PayPal to login </b></a><br>";
				$this->redirect($payPalURL);
			}
		}
	}

	/**
	 * This method is used to process API callbacks
	 *
	 * API callbacks are usually notifications via HTTP POST or less common GET.
	 *
	 * This method should return a PaymentApiResponse
	 *
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	public function notificationCallback(array $options = array())
	{
		// TODO: Implement notificationCallback() method.
	}

	/**
	 * Refunds money
	 *
	 * @param $paymentReference
	 * @param $amount
	 * @param string $comment
	 * @param array $options
	 * @return PaymentApiResponse
	 * @internal param $float
	 */
	public function refund($paymentReference, $amount, $comment = '', array $options = array())
	{
		// TODO: Implement refund() method.
	}

	/**
	 * Cancels a payment
	 *
	 * @param string $paymentReference
	 * @param array $options
	 * @return PaymentApiResponse
	 */
	public function cancel($paymentReference, array $options = array())
	{
		// TODO: Implement cancel() method.
	}
}