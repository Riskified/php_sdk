<?php namespace Riskified\OrderWebhook\Exception;
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
 * Class PropertyException
 * Base Exception for all OrderWebhook issues
 * @package Riskified\OrderWebhook\Exception
 */
class PropertyException extends BaseException {

    protected $object;
    protected $key;
    protected $types;

    public function __construct($object, $key, $types=null) {
        $this->object = $object;
        $this->key = $key;
        $this->types = $types;
        parent::__construct($this->customMessage());
    }

    public function __toString() {
        return get_class($this).': '.$this->customMessage();
    }

    protected function propertyName() {
        return $this->object.'->'.$this->key;
    }

    protected function propertyValue() {
        return $this->object->{$this->key};
    }

    protected function propertyTypes() {
        return join(', ',$this->types);
    }

    protected function customMessage() {
        return 'Property Name:  '.$this->propertyName().
             ', Property Value: '.print_r($this->propertyValue(), true).
             ', Property Type:  '.$this->propertyTypes();
    }

}