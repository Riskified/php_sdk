<?php
// A simple example of creating an order from the command line.
// Usage: php submit.php

include __DIR__.'/../lib/init.php';
use Riskified\SDK as SDK;

# Replace with the 'shop domain' of your account in Riskified
$domain = "busteco.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";

# Change to wh.riskified.com for production
$riskifiedUrl = "sandbox.riskified.com";


# Order
$order = new SDK\Order([
    'id' => '118',
    'name' => 'Order #111',
    'email' => 'great.customer@example.com',
    'total_spent' => 200.0,
    'created_at' => '2014-02-31 14:58:04',
    'closed_at' => '2014-03-31 14:58:05',
    'currency' => 'USD',
    'updated_at' => '2014-02-31 14:58:04',
    'gateway' => 'mypaymentprocessor',
    'browser_ip' => '124.185.86.55',
    'total_price' => 113.23,
    'total_discounts' => 5.0,
    'cancel_reason' => 'inventory',
    'cart_token' => '1sdaf23j212',
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com'
]);

# LineItems   
$lineItem1 = new SDK\LineItem([
	'price' => 100,
	'quantity' => 1,
	'title' => 'ACME Widget',
	'product_id' => '101',
	'sku' => 'ABCD'
]);

$lineItem2 = new SDK\LineItem([
	'price' => 200,
	'quantity' => 4,
	'title' => 'ACME Spring',
	'product_id' => '202',
	'sku' => 'EFGH'
]);
$order->line_items = [$lineItem1, $lineItem2];

# DiscountCodes  
$discountCode = new SDK\DiscountCode([
    'amount' => 19.95,
    'code' => '12'
]);
$order->discount_codes = $discountCode;

# ShippingLines    
$shippingLine = new SDK\ShippingLine([
    'price' => 123.00,
    'title' => 'Free',
]);
$order->shipping_lines = $shippingLine;

# PaymentDetais 
$paymentDetails = new SDK\PaymentDetails([
    'credit_card_bin' => '370002',
    'avs_result_code' => 'Y',
    'cvv_result_code' => 'N',
    'credit_card_number' => 'xxxx-xxxx-xxxx-1234',
    'credit_card_company' => 'VISA'
]);
$order->payment_details = $paymentDetails;

# Customer  
$customer = new SDK\Customer([
    'email' => 'email@address.com',
    'first_name' => 'Firstname',
    'last_name' => 'Lastname',
    'id' => '1233',
    'created_at' => '2012/01/15 11:22:11',
    'orders_count' => 6,
    'verified_email' => true
]);
$order->customer = $customer;

# BillingAddress    
$billingAddress = new SDK\Address([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address1' => '108 Main Street',
    'company' => 'Kansas Computers',
    'country' => 'United States',
    'country_code' => 'US',
    'phone' => '1234567',
    'city' => 'NYC',
    'name' => 'John Doe',
    'address2' => 'Apartment 12',
    'province' => 'New York',
    'province_code' => 'NY',
    'zip' => '64155'
]);
$order->billing_address = $billingAddress;

# ShippingAddress  
$shippingAddress = new SDK\Address([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address1' => '108 Main Street',
    'company' => 'Kansas Computers',
    'country' => 'United States',
    'country_code' => 'US',
    'phone' => '1234567',
    'city' => 'NYC',
    'name' => 'John Doe',
    'address2' => 'Apartment 12',
    'province' => 'New York',
    'province_code' => 'NY',
    'zip' => '64155'
]);
$order->shipping_address = $shippingAddress;

echo 'REQUEST:'.PHP_EOL.json_encode(json_decode($order->toJson()), JSON_PRETTY_PRINT).PHP_EOL;

# Create a curl transport to the Riskified Server    
$transport = new SDK\CurlTransport($domain, $authToken, $riskifiedUrl);
$transport->timeout = 5;

$response = $transport->submitOrder($order);

echo 'RESPONSE:'.PHP_EOL.json_encode($response, JSON_PRETTY_PRINT).PHP_EOL;