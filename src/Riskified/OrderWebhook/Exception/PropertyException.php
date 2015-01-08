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
 * Class PropertyException
 * Base Exception for all OrderWebhook issues
 * @package Riskified\OrderWebhook\Exception
 */
class PropertyException extends BaseException {

    protected $className;
    protected $propertyName;
    protected $value;
    protected $types;

    /**
     * @param string $className of the property
     * @param string $propertyName
     * @param array $types constrains
     * @param mixed $value of the property
     */
    public function __construct($className, $propertyName, $types = null, $value = null) {
        $this->className = $className;
        $this->propertyName = $propertyName;
        $this->value = $value;
        $this->types = $types;
        parent::__construct($this->customMessage());
    }

    public function __toString() {
        return get_class($this).': '.$this->customMessage();
    }

    protected function propertyName() {
        return $this->className.'->'.$this->propertyName;
    }

    protected function propertyValue() {
        if($this->value) {
            return $this->value;
        }
        return '';
    }

    protected function propertyTypes() {
        if($this->types) {
            return join(', ',$this->types);
        }
        return '';
    }

    protected function customMessage() {
        return 'Property Name:  '.$this->propertyName().
             ', Property Value: '.print_r($this->propertyValue(), true).
             ', Property Type:  '.$this->propertyTypes();
    }

}