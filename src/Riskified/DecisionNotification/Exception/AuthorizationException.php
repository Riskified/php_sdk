<?php namespace Riskified\DecisionNotification\Exception;
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

/**
 * Class AuthorizationException
 * Thrown on HMAC mismatch
 * @package Riskified\DecisionNotification\Exception
 */
class AuthorizationException extends NotificationException{

    protected $expected_hmac;
    protected $received_hmac;

    public function __construct($headers, $body, $expected_hmac, $received_hmac) {
        $this->expected_hmac = $expected_hmac;
        $this->received_hmac = $received_hmac;
        parent::__construct($headers, $body);
    }

    protected function customMessage() {
        return parent::customMessage().
        ', Expected HMAC: '.$this->expected_hmac.
        '. Received HMAC: '.$this->received_hmac;
    }
}