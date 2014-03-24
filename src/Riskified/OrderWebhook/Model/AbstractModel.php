<?php namespace Riskified\OrderWebhook\Model;

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
    protected $_fields = [];

    /**
     * Initialize a new model, optionally passing an array of properties
     * @param array $props List of Key => Value pairs for setting model properties
     * @throws \Exception If $props contain an invalid Key
     */
    public function __construct($props = []) {
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
            $this->$key = $value;
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
     * @return bool
     * @throws \Riskified\OrderWebhook\Exception\MultiplePropertiesException
     */
    public function validate() {
        $exceptions = $this->validation_exceptions();
        if ($exceptions)
            throw new Exception\MultiplePropertiesException($exceptions);
        return true;
    }

    /**
     * Validate all fields and nested objects for this object
     * @return array All property validation issues or empty array if no issues found
     */
    protected function validation_exceptions() {
        $exceptions = [];
        foreach ($this->_fields as $key => $value) {
            $types = explode(' ', $value);
            if (is_null($this->$key)) {
                if (end($types) != 'optional')
                    $exceptions[] = new Exception\MissingPropertyException($this, $key, $types);
            } else {
                $exceptions = array_merge($exceptions, $this->validate_key($key, $types));
            }
        }
        return array_filter($exceptions);
    }

    /**
     * Helper method that validates a specific field
     * @param string $value Value to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array All value validation issues or empty array if no issues found
     */
    private function validate_key($key, $types) {
        $value = $this->$key;
        $type = $types[0];
        $exception = [new Exception\TypeMismatchPropertyException($this, $key, $types)];
        switch ($type) {
            case 'string':
                if (!is_string($value))
                    return $exception;
                if (count($types) > 1 && $types[1][0] == '/' && !preg_match($types[1], $value))
                    return [new Exception\FormatMismatchPropertyException($this, $key, $types)];
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
                return $this->validate_object($this, $key, $types, $value);
                break;
            case 'objects':
                return $this->validate_objects($key, $types);
                break;
        }
        return [];
    }

    /**
     * Helper method that validates an object field
     * @param object $that Object to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array|null All object validation issues or null if no issues found
     */
    private function validate_object($that, $key, $types, $object) {
        if (!is_object($object))
            return [new Exception\TypeMismatchPropertyException($that, $key, $types)];

        $parts = explode('\\', get_class($object));
        $class = '\\'.end($parts);

        if (count($types) > 1 && $types[1][0] == '\\' && $class != $types[1]) {
            return [new Exception\ClassMismatchPropertyException($that, $key, $types)];
        }

        return $object->validation_exceptions();
    }

    /**
     * Helper method that validates an objects field
     * @param array|object $objects Array of objects, or single object, to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array|null All objects validation issues or null if no issues found
     */
    private function validate_objects($key, $type) {
        $objects = $this->$key;

        if (is_array($objects)) {
            $type[0] = 'object';
            $exceptions = [];
            foreach ($objects as $object) {
                $exceptions = array_merge($exceptions, $this->validate_object($this, $key, $type, $object));
            }
            return array_filter($exceptions);
        }
        if (is_object($objects)) {
            return $this->validate_object($this, $key, $type, $objects);
        }
        return [new Exception\TypeMismatchPropertyException($this, $key, $type)];
    }

    /**
     * Test if string is a valid date
     * @param string $date String to test
     * @return boolean True if $date is a valid date
     */
    private function is_date($date) {
        date_default_timezone_set('UTC');
        return (strtotime($date));
    }

    /**
     * Get all fields for this model
     * @return array List of all fields
     */
    private function to_array() {
        return $this->process_array(get_object_vars($this));
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
     * @param SimpleXMLElement $xml_order_info Reference to XML object being constructed
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