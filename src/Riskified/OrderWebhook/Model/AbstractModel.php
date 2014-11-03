<?php namespace Riskified\OrderWebhook\Model;
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

use Riskified\Common\Riskified;
use Riskified\OrderWebhook\Exception;

/**
 * Class AbstractModel
 * Abstract superclass for all data models that performs data validation on properties
 * @package Riskified
 */
abstract class AbstractModel {

    /**
     * @internal describes allowed fields and their associated validation requirements
     */
    protected $_fields = array();

    /**
     * Contains all the model properties and their associated values (used by __set and __get)
     * @var array
     */
    protected $_propertyBag = array();

    /**
     * Ignores missing keys during validation when set to False
     * @var boolean
     */
    protected $_enforce_required_keys = true;

    /**
     * Initialize a new model, optionally passing an array of properties
     * @param array $props List of Key => Value pairs for setting model properties
     * @throws \Exception If $props contain an invalid Key
     */
    public function __construct($props = array()) {
        foreach ($props as $key => $value) {
            if (!array_key_exists($key, $this->_fields))
                throw new Exception\InvalidPropertyException($this, $key);
            $this->{$key} = $value;
        }
    }

    /**
     * Magic method to enforce defined fields
     * @param string $key Property to set
     * @param string $value New value of property to set
     * @throws \Exception If $key is invalid
     */
    public function __set($key, $value) {
        if (array_key_exists($key, $this->_fields)) {
            $this->_propertyBag[$key] = $value;
        } else {
            throw new Exception\InvalidPropertyException($this, $key);
        }
    }

    /**
     * Magic method acting as GETTER method for the defined fields
     * @param string $key Property to get
     * @return mixed Value of the property
     * @throws Exception\InvalidPropertyException If $key is invalid
     */
    public function __get($key) {
        if (array_key_exists($key, $this->_fields)) {
            return isset($this->_propertyBag[$key]) ? $this->_propertyBag[$key] : null;
        } else {
            throw new Exception\InvalidPropertyException($this, $key);
        }
    }       

    /**
     * Get the short name of the model, ie. without the namespace prefix
     * @return string Short name of the model
     */
    public function __toString() {
        $parts = explode('\\',get_class($this));
        return end($parts);
    }

    /**
     * Serialize the model to JSON
     * @return string JSON string representation of the model
     */
    public function toJson() {
        return json_encode($this->to_array());
    }

    /**
     * Serialize the model to XML
     * @return string XML string representation of the model
     */
    public function toXml() {
        $xml_order_info = new \SimpleXMLElement('<?xml version="1.0"?><order></order>');
        $this->array_to_xml($this->to_array(), $xml_order_info);
        return $xml_order_info->asXML();
    }

    /**
     * Validate all fields and nested objects for this object
     * @param $enforce_required_keys boolean if FALSE then skip validation of missing fields, only report format exceptions
     * @return bool True if object hierarchy is valid
     * @throws \Riskified\OrderWebhook\Exception\MultiplePropertiesException on any or multiple issues
     */
    public function validate($enforce_required_keys=true) {
        $exceptions = $this->validation_exceptions($enforce_required_keys && !Riskified::$ignore_missing_keys);
        if ($exceptions)
            throw new Exception\MultiplePropertiesException($exceptions);
        return true;
    }

    /**
     * Validate all fields and nested objects for this object
     * @param $enforce_required_keys boolean if FALSE then skip validation of missing fields, only report format exceptions
     * @return array All property validation issues or empty array if no issues found
     */
    protected function validation_exceptions($enforce_required_keys=true) {
        $this->_enforce_required_keys = $enforce_required_keys;
        $exceptions = array();
        foreach ($this->_fields as $propertyName => $constraints) {
            $types = explode(' ', $constraints);
            if (is_null($this->$propertyName)) {
                if ($this->_enforce_required_keys && end($types) != 'optional') {
                    $exceptions[] = new Exception\MissingPropertyException($this, $propertyName, $types);
                }
            } else {
                $exceptions = array_merge($exceptions, $this->validate_key($propertyName, $types, $this->$propertyName));
            }
        }
        return array_filter($exceptions);
    }

    /**
     * validate a specific key
     * @param $key string key name
     * @param $types array constraints
     * @param $value mixed value to validate
     * @return array validation issues found
     */
    private function validate_key($key, $types, $value) {
        $type = $types[0];
        $exception = array(new Exception\TypeMismatchPropertyException($this, $key, $types, $value));
        switch ($type) {
            case 'string':
                if (!is_string($value))
                    return $exception;
                if (count($types) > 1 && $types[1][0] == '/' && !preg_match($types[1], $value))
                    return array(new Exception\FormatMismatchPropertyException($this, $key, $types, $value));
                break;
            case 'number':
                if (!preg_match('/^[0-9]+$/', $value))
                    return $exception;
                break;
            case 'float':
                if (!is_numeric($value))
                    return $exception;
                break;
            case 'boolean':
                if (!is_bool($value))
                    return $exception;
                break;
            case 'date':
                if (!$this->is_date($value))
                    return $exception;
                break;
            case 'object':
                return $this->validate_object($key, $types, $value);
                break;
            case 'array':
                return $this->validate_array($key, $types, $value);
                break;
        }
        return array();
    }

    /**
     * validate a nested object
     * @param $key string key name of object
     * @param $types array constraints
     * @param $object object to validate
     * @return array  All  validation issues or null if no issues found
     */
    private function validate_object($key, $types, $object) {
        if (!is_object($object))
            return array(new Exception\TypeMismatchPropertyException($this, $key, $types, $object));

        $parts = explode('\\', get_class($object));
        $class = '\\'.end($parts);

        if (count($types) > 1 && $types[1][0] == '\\' && $class != $types[1]) {
            return array(new Exception\ClassMismatchPropertyException($this, $key, $types, $object));
        }

        return $object->validation_exceptions($this->_enforce_required_keys);
    }

    /**
     * validate array of values of a simple type
     * @param $key string key name
     * @param $types string constraints
     * @param $array array elements to validate
     * @return array validations issues or null if no issues found
     */
    private function validate_array($key, $types, $array) {

        $childTypes = array_slice($types,1); // remove the 'array' and validate each element by defined type+regex that come after
        if (is_array($array)) {
            $exceptions = array();
            foreach ($array as $element) {
                $exceptions = array_merge($exceptions, $this->validate_key($key, $childTypes, $element));
            }
            return array_filter($exceptions);
        }
        return $this->validate_key($key, $childTypes, $array);
    }

    /**
     * Test if string is a valid date
     * @param string $date String to test
     * @return boolean True if $date is a valid date
     */
    private function is_date($date) {
        return (strtotime($date));
    }

    /**
     * Get all fields for this model
     * @return array List of all fields
     */
    private function to_array() {
        return $this->process_array($this->_propertyBag);
    }

    /**
     * Recursively convert models to arrays
     * @param array $array List of fields to convert from models to arrays
     * @return array Nested array
     */
    private function process_array($array) {
        unset($array['_fields']);
        foreach($array as $key => $value) {
            if (is_null($value))
                unset($array[$key]);
            if (is_object($value))
                $array[$key] = $value->to_array();
            if (is_array($value))
                $array[$key] = $this->process_array($value);
        }
        return $array;
    }

    /**
     * Helper method that converts a nested array to XML
     * @param array $order Nested array to convert
     * @param \SimpleXMLElement $xml_order_info Reference to XML object being constructed
     */
    private function array_to_xml($order, &$xml_order_info) {
        foreach ($order as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)){
                    $subnode = $xml_order_info->addChild($key);
                    $this->array_to_xml($value, $subnode);
                } else{
                    $subnode = $xml_order_info->addChild('item$key');
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml_order_info->addChild($key, htmlspecialchars($value));
            }
        }
    }
}
