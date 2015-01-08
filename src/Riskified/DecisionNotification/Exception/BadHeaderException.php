<?php namespace Riskified\DecisionNotification\Exception;
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

/**
 * Class BadHeaderException
 * Thrown on malformed headers
 * @package Riskified\DecisionNotification\Exception
 */
class BadHeaderException extends NotificationException {

    protected $header;

    public function __construct($headers, $body, $header) {
        $this->header = $header;
        parent::__construct($headers, $body);
    }

    protected function customMessage() {
        return parent::customMessage().
        ', Header: '.$this->header;
    }
}