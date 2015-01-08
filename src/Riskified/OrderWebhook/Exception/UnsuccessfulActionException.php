<?php namespace Riskified\OrderWebhook\Exception;
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

use Riskified\Common\Exception\BaseException;

/**
 * Class FailedTransmissionException
 * On all unsuccessful transmissions, all HTTP responses with code other than 200 OK
 * @package Riskified\OrderWebhook\Exception
 */
class UnsuccessfulActionException extends BaseException {
    protected $jsonResponse;
    protected $statusCode;

    public function __construct($body, $status) {
        $this->jsonResponse = $body;
        $this->statusCode = $status;
        parent::__construct($this->customMessage());
    }

    protected function customMessage() {
        return 'Http Status Code: '.$this->statusCode.
        ', Error was: '.$this->jsonResponse;
    }

    function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }


}