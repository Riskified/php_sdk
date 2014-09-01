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

// Router handler for callback server
// See run_callback_server.sh for usage

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Signature;
use Riskified\DecisionNotification\Model;

# Replace with the 'shop domain' of your account in Riskified
$domain = "test.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "1388add8a99252fc1a4974de471e73cd";

Riskified::init($domain, $authToken);

$signature = new Signature\HttpDataSignature();

$valid_headers = array(
    $signature::HMAC_HEADER_NAME
);

function map_keys($header, $value) {
    $canonical_header = str_replace('HTTP-','', str_replace('_', '-', strtoupper(trim($header))));
    return array ($canonical_header => $value);
};

function reduce_keys($carry, $item) {
    if (is_null($carry)) {
        $carry=array();
    }
    return array_merge($carry, $item);
};

$canonical_headers = array_reduce(array_map('map_keys', array_keys($_SERVER), $_SERVER), 'reduce_keys');

$body = @file_get_contents('php://input');
$headers = array_intersect_key($canonical_headers, array_flip($valid_headers));

$notification = new Model\Notification($signature, $headers, $body);
$msg = "Order #$notification->id changed to status '$notification->status' with message '$notification->description'\n";

$output = fopen('php://stdout', 'w');
fputs($output, $msg);
fclose($output);

return true;