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

use Riskified\Common\Exception\BaseException;

/**
 * Class NotificationException
 * Base exception for issues with the Notification Model
 * @package Riskified\DecisionNotification\Exception
 */
class NotificationException extends BaseException {

    protected $headers;
    protected $body;

    public function __construct($headers, $body) {
        $this->headers = $headers;
        $this->body = $body;
        parent::__construct($this->customMessage());
    }

    protected function headersString() {
        return '[ '.join(', ',$this->headers).' ]';
    }

    protected function customMessage() {
        return 'Headers: '.$this->headersString().
        ', Body: '.$this->body;
    }


}