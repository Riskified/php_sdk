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

// A simple example of creating a marketplace order.
// Usage: php order_marketplace_create.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Validations;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

# Replace with the 'shop domain' of your account in Riskified
$domain = "test.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "1388add8a99252fc1a4974de471e73cd";

Riskified::init($domain, $authToken, Env::SANDBOX, Validations::IGNORE_MISSING);

# Order
$order = new Model\Order(array(
    'id' => '1234',
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
            'sku' => 'ABCD',
            'condition' => 'mint',
            'seller' => new Model\Seller(array(
                'customer' => new Model\Customer(array(
                    'email' => 'another@address.com',
                    'first_name' => 'SellerFirstname',
                    'last_name' => 'SellerLastname',
                    'id' => '4567',
                    'created_at' => '2008-01-10T11:00:00-05:00',
                    'orders_count' => 16,
                    'account_type' => 'premium',
                    'verified_email' => true
                )),
                'correspondence' => 77,
                'price_negotiated' =>  false,
                'starting_price' => 100.3
            ))
        ))
    ),
    'discount_codes' => new Model\DiscountCode(array(
        'amount' => 19.95,
        'code' => '12'
    )),
    'shipping_lines' => new Model\ShippingLine(array(
        'price' => 123.00,
        'code' => 'Free',
    )),
    'payment_details' => new Model\PaymentDetails(array(
        'credit_card_bin' => '370002',
        'avs_result_code' => 'Y',
        'cvv_result_code' => 'N',
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
        'account_type' => 'free',
        'social' => array(
            new Model\SocialDetails(array(
                'network' => 'internal',
                'public_username' => 'donnie7',
                'community_score' => 68,
                'profile_picture' => 'http://img.com/abc.png',
                'email' => 'donnie@mail.com',
                'bio' => 'avid mountaineer...',
                'account_url' => 'http://shop.com/user/donnie7',
                'following' => 231,
                'followed' => 56,
                'posts' => 10
            )),
            new Model\SocialDetails(array(
                'network' => 'facebook',
                'public_username' => '7231654'
            ))
        )
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
    )),
    'nocharge_amount' => new Model\RefundDetails(array(
        'refund_id' => '1235',
        'amount' => 20,
        'currency' => 'USD',
        'reason' => 'wallet'
    ))
));

echo "\nORDER REQUEST:".PHP_EOL.json_encode(json_decode($order->toJson())).PHP_EOL;


# Create a curl transport to the Riskified Server    
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;


try {
    $response = $transport->createOrder($order);
    echo PHP_EOL."Create Order succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;
} catch(\Riskified\OrderWebhook\Exception\UnsuccessfulActionException $uae) {
    echo PHP_EOL."Create Order not succeeded. Status code was: ".$uae->statusCode." and json body was: "
        .json_encode($uae->jsonResponse).PHP_EOL;
} catch(Exception $e) {
    echo PHP_EOL."Create Order not succeeded. Exception: ".$e->getMessage().PHP_EOL;
}