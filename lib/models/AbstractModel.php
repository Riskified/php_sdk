<?php namespace Riskified\SDK {
    abstract class AbstractModel {

        protected $_fields = [];

        public function __construct($props = []) {
            foreach ($props as $key => $value) {
                if (!array_key_exists($key, $this->_fields))
                    throw new \Exception("set(): Property ${this}->${key} does not exist");
                $this->{$key} = $value;
            }
        }

        public function __set($key, $value) {
            if (array_key_exists($key, $this->_fields)) {
                $this->$key = $value;
            } else {
                throw new \Exception("set(): Property ${this}->${key} does not exist");
            }
        }

        public function __toString() {
            return end(explode('\\',get_class($this)));
        }


        public function toJson() {
            return json_encode($this->to_array());
        }

        public function toXml() {
            $xml_order_info = new SimpleXMLElement('<?xml version="1.0"?><order></order>');
            $this->array_to_xml($this->to_array(), $xml_order_info);
            return $xml_order_info->asXML();
        }


        public function validate() {
            $valid = [];
            foreach ($this->_fields as $key => $value) {
                $type = explode(' ', $value);
                if (is_null($this->$key)) {
                    if (end($type) != 'optional')
                        $valid[] = "${this}->${key}: Not set, expecting ".strtoupper(reset($type));
                } else {
                    $valid = array_merge($valid, $this->validate_value($this->$key, reset($type), "${this}->${key}"));
                }
            }
            return array_filter($valid);
        }


        private function validate_value($value, $type, $err_prefix) {
            $err_msg = ["$err_prefix: Type Mismatch: got ".strtoupper(gettype($value))." ($value), expecting ".strtoupper($type)];
            switch ($type) {
                case 'string':
                    if (!is_string($value))
                        return $err_msg;
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
                    return is_object($value) ? $value->validate() : $err_msg;
                    break;
                case 'objects':
                    $valid = $this->validate_objects($value, $err_prefix);
                    return is_array($valid) ? $valid : $err_msg;
                    break;
            }
            return [];
        }

        private function validate_objects($objects, $err_prefix) {
            if (is_array($objects)) {
                $valid = [];
                foreach ($objects as $object) {
                    $valid = array_merge($valid, $this->validate_value($object, 'object', $err_prefix));
                }
                return array_filter($valid);
            }
            if (is_object($objects)) {
                return $objects->validate();
            }
            return null;
        }

        private function is_date($date) {
            date_default_timezone_set('UTC');
            return (strtotime($date));
        }

        private function to_array() {
            return $this->process_array(get_object_vars($this));
        }

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
}