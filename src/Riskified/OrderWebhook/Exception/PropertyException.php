<?php namespace Riskified\OrderWebhook\Exception;

use Riskified\Common\Exception\BaseException;

class PropertyException extends BaseException {

    protected $object;
    protected $key;
    protected $types;

    public function __construct($object, $key, $types=null) {
        $this->object = $object;
        $this->key = $key;
        $this->types = $types;
        parent::__construct($this->message, 100);
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
        return 'Property Name: '.$this->propertyName().', Property Value: '.$this->propertyValue().', Property Type: '.$this->propertyTypes();
    }

}