<?php namespace riskified\sdk {
abstract class Base {

	public function __construct() {
		foreach ($this->_fields as $key => $value) {
		  	$this->{$key} = null;
		}
	}

	public function __set($key, $value) {
		if (array_key_exists($key, $this->_fields)) {
		  	$this->$key = $value;
		} else {
		  	throw new \Exception('set(): Property '.$this.'->'.$key.' does not exist');
		}
	}
	
	public function __toString() {
        return end(explode('\\',get_class($this)));
    }

	public function validate() {
		$valid = array();
		foreach ($this->_fields as $key => $value) {
			$type = explode(' ', $value);		
		  	if (is_null($this->$key)) {
		  		if (end($type) != 'optional') {
					$valid[] = $this.'->'.$key." is null and not optional";
		  		}
		  	} else {
		  		$valid = array_merge($valid, $this->validate_key($key, reset($type)));
			}
		}
		return array_filter($valid);
	}
	
	public function to_json() {
		return json_encode($this->to_array());
	}

	public function to_xml() {
		$order = $this->to_array();
		$xml_order_info = new SimpleXMLElement("<?xml version=\"1.0\"?><order></order>");
		$this->array_to_xml($order, $xml_order_info);
		return $xml_order_info->asXML();
	}
	
	private function validate_key($key, $type) {
		$value = $this->$key;
		$mismatch = $this.'->'.$key.' type mismatch (\''.$value.'\'), expecting ';
	  	switch ($type) {
			case 'string':
				if (!preg_match('/\w+/', $value)) {
					return array($mismatch.'string');
				}
				break;
			case 'number':
				if (!preg_match('/\d+/', $value)) {
					return array($mismatch.'number');
				}
				break;
			case 'date':
				if (!$this->validate_date($value)) {
					return array($mismatch.'date');
				}
				break;
			case 'object':
				if (!is_object($value)) {
					return array($mismatch.'object');
				}
				return $value->validate();
				break;				
			case 'objects':
				return $this->validate_objects($value, $key);
				break;								
	  	}
	  	return array();
	}
	
	private function validate_objects($objects, $key) {
		if (is_array($objects)) {
			$valid = array();
			foreach ($objects as $object) {
				if (!is_object($object)) {
					$valid[] = array($this.'->'.$key.' type mismatch (\''.$object.'\'), expecting object');
				} else {
					$valid = array_merge($valid, $object->validate());
				}
			}
			return array_filter($valid);
		}
		if (is_object($objects)) {
			return $objects->validate();
		}
		return array($this.'->'.$key.' type mismatch (\''.$objects.'\'), expecting objects');
	}
		
	private function validate_date($datetime) {
		date_default_timezone_set('UTC');
		$timeStamp = strtotime($datetime);
		return ($timeStamp);
	}
	
	private function to_array() {
		return $this->process_array(get_object_vars($this));
	}

	private function process_array($array) {
		unset($array['_fields']);
		foreach($array as $key => $value) {
			if (is_null($value)) {
				unset($array[$key]);
			}
			if (is_object($value)) {
				$array[$key] = $value->to_array();
			}
			if (is_array($value)) {
				$array[$key] = $this->process_array($value);
			}
		}
		return $array;
	}

	private function array_to_xml($order, &$xml_order_info) {           
		foreach($order as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)){
					$subnode = $xml_order_info->addChild("$key");
					$this->array_to_xml($value, $subnode);
				}
				else{
					$subnode = $xml_order_info->addChild("item$key");
					$this->array_to_xml($value, $subnode);
				}
			}
			else {
				$xml_order_info->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}

}
}?>