<?php
abstract class Base {
   
    public function toArray() {
        return $this->processArray(get_object_vars($this));
    }
   
    private function processArray($array) {
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
    
    private function  array_to_xml($order, &$xml_order_info) {
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
