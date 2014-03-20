<?php
abstract class Base {

	public function __construct() {
		foreach ($this->_fields as $key => $value) {
		  $this->{$key} = '';
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
		  $keyValid = true;
		  switch ($value) {
		    case 'string':
			    $keyValid = preg_match('/\w+/', $this->$key);
			    break;
			case 'number':
			    $keyValid = preg_match('/\d+/', $this->$key);
			    break;
			case 'date':
				$keyValid = $this->validateDate($this->$key);
				break;
			case 'object':
				$keyValid = ($this->$key != null && $this->$key->validate());
				break;				
		  }
		  if (!$keyValid) {
			echo("validation failed on property '".$key."' in object of class '".get_class($this)."'\n");
			$objectValid = false;
		  }
		}
		return $objectValid;
	}
		
	private function validateDate($datetime) {
		date_default_timezone_set('UTC');
		$timeStamp = strtotime($datetime);
		if($timeStamp)
			return true;
		else
			return false;
	}
  
	public function toArray() {
		return $this->processArray(get_object_vars($this));
	}

	private function processArray($array) {
		unset($array['_fields']);
		foreach($array as $key => $value) {
			if (is_object($value)) {
				$array[$key] = $value->toArray();
			}
			if (is_array($value)) {
				$array[$key] = $this->processArray($value);
			}
		}
		return $array;
	}

	public function __toJson() {
		return json_encode($this->toArray());
	}

	public function __toXml() {
		$order = $this->toArray();
		$xml_order_info = new SimpleXMLElement("<?xml version=\"1.0\"?><order></order>");
	
		$this->array_to_xml($order,$xml_order_info);
	
		return $xml_order_info->asXML();
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
?>