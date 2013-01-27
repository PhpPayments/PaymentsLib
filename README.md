# CakePHP Payments Plugin #

http://github.com/PhpPayments/PaymentsLib

This plugin is thought to provide a generic API for different payment processors.

The idea is to wrap every payment processor into the same interface so that it becomes immediately usable by every developer who is familiar with this API, removing the need to learn all the different payment providers and their specific APIs and SDKs.

The processors should not depend on anything else than this API and be useable within any application, even shell scripts.

Contributions are welcome!

## Requirements

 * php 5.3 or greater

This plugin is just an API and set of interfaces it does not contain any processors, you'll have to pick and add processors for your app.

### List of Open Source Payment Processors using this API

Please create a ticket on github or send an email if you want to get your processor on this list.

 * Sofort.de (LGPL License) - http://github.com/PhpPayments/Sofort

### List of Commercial Payment Processors using this API

Contact us to get your plugin reviewed and added to this list if it matches the acceptance criteria. A good processor has proper value validation, error handling and logging.

There are no commercial processors available yet.

## Implementing your processor based on this API

All of the following steps are considered as required to write a proper and as good as possible fail save and easy to use payment processor:

* Your processor has to extend BasePaymentProcessor and use the interfaces as needed
* Your processor must not have any application specific or dependant code in it
* You have to use set() to set values for the API / processor and validateValues() to check if all required values for a call are present
* You have to use the Exceptions from the Payments plugin to encapsulate payment gateway API errors and payment processor issues
* You have to map the payment statuses from the foreign APIs to the constants of the PaymentStatus class and return them instead the foreign statuses
* Your processor should not have hard dependencies on anything else if possible
* Use the PaymentApiLog to log payment related messages

Contact us to get your processor reviewed and added to the processor list if it matches the acceptance criterias.

### Configuration of Processors

All Payment processors must follow this convention for configuration data

	'SomePaymentProcessor' => array(
		'sandboxMode' => false,
		'default' => array(
			'apiKey' => '11223:123456:h25lh252525hlhadslgh2362l6h2lsfg'
			'apiId' => '151611574',
			'...' => '...'
		),
		'sandbox' => array(
			'apiKey' => '33221:652141:kl262lhsdgh15dslhgslhj325lhdsglsd'
			'apiId' => '623512526',
			'...' => '...'
		),
		'secondLiveConfig' => array(
			'...' => '...',
		),
	),

sandboxMode mode and live are required, sandbox also if a sandbox configuration is available. sandboxMode can be true or false to switch between live and sandbox configuration.

### Sandbox mode

You'll have to call YourPaymentProcessor::sandboxMode(true) or YourPaymentProcessor::sandboxMode(false) to set a payment processor into sandbox mode. This is important to toggle between live and sandbox settings and special testing variables and URLs most sandboxes require. To get the current state of a processor just call YourPaymentProcessor::sandboxMode() without passing an argument.

### Recommended field names

To make it easier for everyone to use different processors without the need to map the fields of the app to all the processors in a different way the following field names are recommended. If you want to get added to the processor list above you'll have to follow the conventions.

Not all of these fields are required by each processor. If they match what you need use them. Do not use other names!

Generic fields:

* amount
* currency
* vat
* payment_reason
* payment_reason2
* payment_reference
* customer_email
* customer_first_name
* customer_last_name
* customer_email
* customer_first_name
* customer_last_name
* customer_phone
* customer_street
* customer_address
* customer_address2
* customer_zip
* customer_country
* customer_state
* customer_description
* customer_iban - Bank account number
* customer_bic - Bank id
* customer_account_id - Can be used for payment systems using something else than email or iban/bic
* billing_address
* billing_address2
* billing_zip
* billing_city
* billing_country
* billing_state

For Credit Card processors

* card_number
* card_code
* card_holder
* card_month - Expiration date month
* card_year - Expiration date year

For recurring payments

* subscription_reference
* recurring_trial_amount
* recurring_start_data - Format: (YYYY-MM-DD)
* recurring_end_date - Format: (YYYY-MM-DD)
* recurring_interval
* recurring_frequency
* recurring_occurence
* recurring_trial_occurence

Custom fields:

* custom1
* custom2
* custom3
* ...

## Logging

The payments lib comes with a very basic file logger to log errors. You can inject whatever log object from your app or framework you want, the only requirement is that you'll have to wrap it in a class that will implement the Paylemt\Log\LogInterface.

It is required to implement a write() method that takes a message (can by any data type) and an optional log type, default is "debug".

To pass your custom log object just add it to the config of the processor:

	$yourLogger = $yourLogger;
	$Processor = new \Payment\Processor\YourProcessor\Processor(array(
		'logObject' => $yourLogger);

## cURL Wrapper

This plugin also comes with a Curl class to wrap the native php cURL functionality in object oriented fashion.

Please use it instead of any other 3rd party libs in your processors.

## Example of working with a processor

### Doing a payment

First get an instance of your payment processor and pass the API configuration.

	$config = array(
		'apiKey' => 'YOU-API-KEY',
		'whatEverElse' => 'isNeeded');
	$Processor = new \Payment\Processor\YourProcessor\Processor($config);

Note that different processors might require different fields.

	$Processor->set('payment_reason', 'Order 123'); // required
	$Processor->set('payment_reason2', 'Something here'); // optional

Call the pay method.

	$Processor->pay(15.99);

## Support

For support and feature request, please visit the Payments issue page

https://github.com/PhpPayments/Payments/issues

## License

Copyright 2013, Florian Krämer

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.