<?php
/**
 * Copyright 2013-2015 Riskified.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.html
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

// A simple example of submitting an order.
// Usage: php order_simple_submit.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Validations;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

# Replace with the 'shop domain' of your account in Riskified
$domain = "[your shop domain as registered to Riskified]";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "[your authentication token string]";

Riskified::init($domain, $authToken, Env::SANDBOX, Validations::IGNORE_MISSING);

# Order
$order = new Model\Order(array(
    'id' => '1234phpsdksimple',
    'email' => 'great.customer@example.com',
    'created_at' => '2018-08-22T11:00:00-05:00',
    'currency' => 'USD',
    'updated_at' => '2018-08-22T11:00:00-05:00',
    'gateway' => 'mypaymentprocessor',
    'browser_ip' => '124.185.86.55',
    'total_price' => 113.23,
    'total_discounts' => 5.0,
    'cart_token' => '1sdaf23j212',
    'device_id' => '01234567-89ABCDEF-01234567-89ABCDEF',
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com',
    'source' => 'desktop_web'
));

# LineItems
$lineItem1 = new Model\LineItem(array(
    'price' => 100,
    'quantity' => 1,
    'title' => 'ACME Widget',
    'product_id' => '101',
    'sku' => 'ABCD',
    'registry_type' => 'baby'
));
$lineItem2 = new Model\LineItem(array(
    'price' => 200,
    'quantity' => 4,
    'title' => 'ACME Spring',
    'product_id' => '202',
    'sku' => 'EFGH',
    'registry_type' => 'wedding'
));
$lineItem3 = new Model\LineItem(array(
    'price' => 150,
    'quantity' => 2,
    'title' => "New York Yankees game",
    'section' => 'Bleachers',
    'city' => 'New York City',
    'event_date' => '2019-07-12T19:00:00-4:00',
    'country_code' => 'US',
    'latitude' => '40.8296 N',
    'longitude' => '73.9262 W',
    'product_type' => 'event'
));
$order->line_items = array($lineItem1, $lineItem2, $lineItem3);

# DiscountCodes
$discountCode = new Model\DiscountCode(array(
    'amount' => 19.95,
    'code' => '12'
));
$order->discount_codes = array($discountCode);

# ShippingLines
$shippingLine = new Model\ShippingLine(array(
    'price' => 123.00,
    'title' => 'Free Shipping'
));
$order->shipping_lines = array($shippingLine);

# PaymentDetails
$paymentDetails = new Model\PaymentDetails(array(
    'credit_card_bin' => '370002',
    'avs_result_code' => 'Y',
    'cvv_result_code' => 'N',
    'credit_card_number' => 'xxxx-xxxx-xxxx-1234',
    'credit_card_company' => 'VISA'
));
$order->payment_details = array($paymentDetails);

# Customer
$customer = new Model\Customer(array(
    'email' => 'email@address.com',
    'first_name' => 'Firstname',
    'last_name' => 'Lastname',
    'id' => '1233',
    'created_at' => '2008-01-10T11:00:00-05:00',
    'orders_count' => 6,
    'verified_email' => true,
    'account_type' => 'free'
));
$order->customer = $customer;

# BillingAddress
$billingAddress = new Model\Address(array(
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
));
$order->billing_address = $billingAddress;

# ShippingAddress
$shippingAddress = new Model\Address(array(
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
));
$order->shipping_address = array($shippingAddress);

echo "\nORDER REQUEST:".PHP_EOL.json_encode(json_decode($order->toJson())).PHP_EOL;


# Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;


try {
    $response = $transport->submitOrder($order);
    echo PHP_EOL."Submit Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;
} catch(\Riskified\OrderWebhook\Exception\UnsuccessfulActionException $uae) {
    echo PHP_EOL."Submit Order not succeeded. Status code was: ".$uae->statusCode." and json body was: "
        .json_encode($uae->jsonResponse).PHP_EOL;
} catch(Exception $e) {
    echo PHP_EOL."Submit Order not succeeded. Exception: ".$e->getMessage().PHP_EOL;
}
