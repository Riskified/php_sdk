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
 * Class MultiplePropertiesException
 * thrown at the end of an unsuccessful validation, holds a collection of $exceptions encountered
 * @package Riskified\OrderWebhook\Exception
 */
class MultiplePropertiesException extends BaseException {

    public $exceptions;

    public function __construct($exceptions) {
        $this->exceptions = $exceptions;
        parent::__construct($this->customMessage());
    }

    public function customMessage() {
        $sep = PHP_EOL.' ';
        return $this->count_string().$sep.join($sep, $this->exceptions).PHP_EOL;
    }

    protected function count_string() {
        return 'Exception count: '.count($this->exceptions);
    }

} 