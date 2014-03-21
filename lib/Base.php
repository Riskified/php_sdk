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
		  	throw new Exception('set: property "'.$key.'" does not exist on class "'.get_class($this).'"');
		}
	}

	public function validate() {
		$objectValid = true;
		foreach ($this->_fields as $key => $value) {
			$type = explode(' ', $value);		
		  	if (is_null($this->$key)) {
		  		if (end($type) != 'optional') {
		  			echo ("missing property '".$key."' in object of class '".get_class($this)."'\n");
					$objectValid = false;
		  		}
		  	} else {
		  		$keyValid = $this->validate_key($this->$key, reset($type));
			  	if (!$keyValid) {
			  		# TODO remove
					echo ("validation failed on property '".$key."' in object of class '".get_class($this)."'\n");
					$objectValid = false;
			  	}
			}
		}
		return $objectValid;
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
	  	switch ($type) {
			case 'string':
				return preg_match('/\w+/', $key);
				break;
			case 'number':
				return preg_match('/\d+/', $key);
				break;
			case 'date':
				return $this->validate_date($key);
				break;
			case 'object':
				return (is_object($key) && $key->validate());
				break;				
			case 'objects':
				return $this->validate_objects($key);
				break;								
	  	}
	}
	
	private function validate_objects($objects) {
		if (is_array($objects)) {
			$valid = true;
			foreach ($objects as $object) {
				$valid = $valid && is_object($object) && $object->validate();
			}
			return $valid;
		}
		if (is_object($objects)) {
			return $objects->validate();
		}
		return false;
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