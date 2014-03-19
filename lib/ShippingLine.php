<?php

class ShippingLine extends Base{

  /**
   * 
   * REQUIRED
   * 
   * The ShippingLine class should contain the following fields:
   * price
   * title 
   */ 
    
    protected $price;   #required - float
    protected $title;   #required - string
    protected $code;    #optional - string     ----- is this required? (appear in integration_spec)
    
    public function setPrice($price) {
        if(!$price)
            throw new Exception('The Price field is required');
        else
            $this->price = $price;
    }
    
    public function setTitle($title) {
        if(!$title)
            throw new Exception('The Title field is required');
        else
            $this->title = $title;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
   
}
