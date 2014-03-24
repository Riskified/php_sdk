<?php
// A simple example simulating receiving a callback on order decision
// Usage: php callback.php

include __DIR__.'/../src/Riskified/autoloader.php';
use Riskified\Common\Riskified;
use Riskified\Common\Signature;
use Riskified\DecisionNotification\Model;

# Replace with the 'shop domain' of your account in Riskified
$domain = "busteco.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";

Riskified::init($domain, $authToken);

$signature = new Signature\HttpDataSignature();

$headers = ['X-Riskified-Hmac-Sha256:4e17669551be731365461a27bf50d6886f11f2fd95ba88c74d401d0328909a63'];
$body = 'id=1&status=approved';

$notification = new Model\Notification($signature, $headers, $body);

print "Order $notification->id changed to status $notification->status";