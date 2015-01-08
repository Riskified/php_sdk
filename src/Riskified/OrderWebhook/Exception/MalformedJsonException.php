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
 * Class MalformedJsonException
 * thrown on JSON response parsing issues
 * @package Riskified\OrderWebhook\Exception
 */
class MalformedJsonException extends BaseException {

    protected $body;
    protected $status;

    public function __construct($body, $status) {
        $this->body = $body;
        $this->status = $status;
        parent::__construct($this->customMessage());
    }

    protected function customMessage() {
        return 'Status Code: '.$this->status.
        ', Body: '.$this->body;
    }


}