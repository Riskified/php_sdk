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

// An example of a complete order flow.
// Usage: php order_full_flow.php

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

Riskified::init($domain, $authToken, Env::SANDBOX, Validations::ALL);

$order_details = array(
    'id' => 'ch567',
    'name' => '#1234',
    'email' => 'great.customer@example.com',
    'created_at' => '2010-01-10T11:00:00-05:00',
    'closed_at' => null,
    'currency' => 'CAD',
    'updated_at' => '2010-01-10T11:00:00-05:00',
    'gateway' => 'mypaymentprocessor',
    'browser_ip' => '124.185.86.55',
    'total_price' => 113.23,
    'total_discounts' => 5.0,
    'cart_token' => '1sdaf23j212',
    'additional_emails' => array('my@email.com','second@email.co.uk'),
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com',
    'line_items' => array(
        new Model\LineItem(array(
            'price' => 100,
            'quantity' => 1,
            'title' => 'ACME Widget',
            'product_id' => '101',
            'sku' => 'ABCD'
        )),
        new Model\LineItem(array(
            'price' => 200,
            'quantity' => 4,
            'title' => 'ACME Spring',
            'product_id' => '202',
            'sku' => 'EFGH',
            'category' => 'ACME Spring Category'
        ))
    ),
    'discount_codes' =>  new Model\DiscountCode(array(
        'amount' => 19.95,
        'code' => '12'
    )),
    'shipping_lines' => new Model\ShippingLine(array(
        'title' => 'FedEx',
        'price' => 123.00,
        'code' => 'Free',
    )),
    'payment_details' => new Model\PaymentDetails(array(
        'credit_card_bin' => '370002',
        'credit_card_number' => 'xxxx-xxxx-xxxx-1234',
        'credit_card_company' => 'VISA'
    )),
    'customer' => new Model\Customer(array(
        'email' => 'email@address.com',
        'first_name' => 'Firstname',
        'last_name' => 'Lastname',
        'id' => '1233',
        'created_at' => '2008-01-10T11:00:00-05:00',
        'orders_count' => 6,
        'verified_email' => true,
        'account_type' => 'free'
    )),
    'billing_address' => new Model\Address(array(
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
    )),
    'shipping_address' => new Model\Address(array(
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
    ))
);


# Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;


#### Create Checkout
$checkout = new Model\Checkout($order_details);

$response = $transport->createCheckout($checkout);
echo PHP_EOL."Create Checkout succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Notify Checkout Denied
# $response = $transport->deniedCheckout($checkout);
# echo PHP_EOL . "Denied Checkout succeeded. Response: " . PHP_EOL . json_encode($response) . PHP_EOL;


#### Create and Submit Order
$order = new Model\Order($order_details);
$order->checkout_id = $order->id;
$order->id = 'or1234';
$order->payment_details->avs_result_code = 'Y';
$order->payment_details->cvv_result_code = 'N';

$response = $transport->createOrder($order);
echo PHP_EOL."Create Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;

$response = $transport->submitOrder($order);
echo PHP_EOL."Submit Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Update Order
$updatedOrder = new Model\Order(array(
    'id' => $order->id,
    'email' => 'another.email@example.com',
));

$response = $transport->updateOrder($updatedOrder);
echo PHP_EOL."Update Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Partially Refund Order
$refund = new Model\Refund(array(
    'id' => $order->id,
    'refunds' => array(new Model\RefundDetails(array(
        'refund_id' => 'refund_001',
        'amount' => 33.12,
        'currency' => 'USD',
        'reason' => 'Product Missing'
    )))
));
$response = $transport->refundOrder($refund);
echo PHP_EOL."Refund Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Notify Order Fulfillment
$fullfillments = new Model\Fulfillment(array (
    'id' => $order->id,
    'fulfillments' => array(new Model\FulfillmentDetails(array(
        'fulfillment_id' => '123',
        'created_at' =>  '2013-04-23T13:36:50Z',
        'status' => 'success',
        'tracking_company' =>  'fedex',
        'tracking_numbers' => 'abc123',
        'tracking_urls' => 'http://fedex.com/track?q=abc123',
        'message' => 'estimated delivery 2 days',
        'receipt' => 'authorization: 765656'
    )))
));

$response = $transport->fulfillOrder($fullfillments);
echo PHP_EOL."Fulfill Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;


#### Cancel (Full Refund) Order
$response = $transport->cancelOrder($order);
echo PHP_EOL."Cancel Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;