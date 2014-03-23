<?php namespace Riskified\OrderWebhook\Model;
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
                throw new \Exception("set(): Property ${this}->${key} does not exist");
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
            throw new \Exception("set(): Property ${this}->${key} does not exist");
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
     * Validate all fields and nested objects
     * @return array All property validation issues or empty array if no issues found
     */
    public function validate() {
        $valid = [];
        foreach ($this->_fields as $key => $value) {
            $type = explode(' ', $value);
            if (is_null($this->$key)) {
                if (end($type) != 'optional')
                    $valid[] = "${this}->${key}: Not set, expecting ".strtoupper($type[0]);
            } else {
                $valid = array_merge($valid, $this->validate_value($this->$key, $type, "${this}->${key}"));
            }
        }
        return array_filter($valid);
    }

    /**
     * Helper method that validates a specific field
     * @param string $value Value to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array All value validation issues or empty array if no issues found
     */
    private function validate_value($value, $list, $err_prefix) {
        $type = $list[0];
        $err_msg = ["$err_prefix: Type Mismatch: got ".strtoupper(gettype($value))." ($value), expecting ".strtoupper($type)];
        switch ($type) {
            case 'string':
                if (!is_string($value))
                    return $err_msg;
                if (count($list) > 1 && $list[1][0] == '/' && !preg_match($list[1], $value))
                    return ["$err_prefix: Bad Format: ($value) should match ".$list[1]];
                break;
            case 'number':
                if (!preg_match('/^[0-9]+$/', $value))
                    return $err_msg;
                break;
            case 'float':
                if (!is_numeric($value))
                    return $err_msg;
                break;
            case 'boolean':
                if (!is_bool($value))
                    return $err_msg;
                break;
            case 'date':
                if (!$this->is_date($value))
                    return $err_msg;
                break;
            case 'object':
                $valid = $this->validate_object($value, $list, $err_prefix);
                return is_array($valid) ? $valid : $err_msg;
                break;
            case 'objects':
                $valid = $this->validate_objects($value, $list, $err_prefix);
                return is_array($valid) ? $valid : $err_msg;
                break;
        }
        return [];
    }

    /**
     * Helper method that validates an object field
     * @param object $object Object to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array|null All object validation issues or null if no issues found
     */
    private function validate_object($object, $list, $err_prefix) {
        if (!is_object($object))
            return null;

        $parts = explode('\\', get_class($object));
        $class = '\\'.end($parts);
        if (count($list) > 1 && $list[1][0] == '\\' && $class != $list[1])
            return ["$err_prefix: Object Mismatch: got ".$class.", expected ".$list[1]];
        return $object->validate();
    }

    /**
     * Helper method that validates an objects field
     * @param array|object $objects Array of objects, or single object, to validate
     * @param array $list Description of validation constraints
     * @param string $err_prefix Prefix to prepend to validation issue messages
     * @return array|null All objects validation issues or null if no issues found
     */
    private function validate_objects($objects, $list, $err_prefix) {
        if (is_array($objects)) {
            $list[0] = 'object';
            $valid = [];
            foreach ($objects as $object) {
                $valid = array_merge($valid, $this->validate_value($object, $list, $err_prefix));
            }
            return array_filter($valid);
        }
        if (is_object($objects)) {
            return $this->validate_object($objects, $list, $err_prefix);
        }
        return null;
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