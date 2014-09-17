<?php
/**
 * Copyright 2013-2014 Riskified.com, Inc. or its affiliates. All Rights Reserved.
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

// A simple example of uploading historical orders from the command line.
// Usage: php upload_historical.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Env;
use Riskified\Common\Signature;
use Riskified\OrderWebhook\Model;
use Riskified\OrderWebhook\Transport;

# Replace with the 'shop domain' of your account in Riskified
$domain = "test.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "1388add8a99252fc1a4974de471e73cd";

Riskified::init($domain, $authToken, Env::STAGING);

$first_order = new Model\Order(array(
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
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com'
));

$second_order = new Model\Order(array(
    'id' => '1235',
    'name' => '#1235',
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
    'note' => 'Shipped to my hotel.',
    'referring_site' => 'google.com'
));

$orders = array($first_order, $second_order);

# Create a curl transport to the Riskified Server
$transport = new Transport\CurlTransport(new Signature\HttpDataSignature());
$transport->timeout = 10;

try {
    $response = $transport->sendHistoricalOrders($orders);
    echo PHP_EOL."Upload succeeded. Response: ".PHP_EOL.json_encode($response).PHP_EOL;
} catch(\Riskified\OrderWebhook\Exception\UnsuccessfulActionException $uae) {
    echo PHP_EOL."Upload failed. Status code was: ".$uae->statusCode." and body was: "
        .json_encode($uae->jsonResponse).PHP_EOL;
} catch(Exception $e) {
    echo PHP_EOL."Upload failed. Exception: ".$e->getMessage().PHP_EOL;
}
