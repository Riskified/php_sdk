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

// A simple example simulating receiving a callback on order decision
// Usage: php callback.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Signature;
use Riskified\DecisionNotification\Model;

# Replace with the 'shop domain' of your account in Riskified
$domain = "test.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";

Riskified::init($domain, $authToken);

$signature = new Signature\HttpDataSignature();

$headers = array('X-Riskified-Hmac-Sha256:cdaa895c1716784598f6a8a9ef9f35c68c2d699fe93364fff87a42c2af6c9a7f');
$body = '{"order":{"id":1,"description":"all good","status":"approved"}}';

$notification = new Model\Notification($signature, $headers, $body);

print "Order #$notification->id changed to status '$notification->status' with message '$notification->description'\n";
