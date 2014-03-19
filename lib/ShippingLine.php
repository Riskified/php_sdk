<?php

class ShippingLine extends Base{

  /**
   * The ShippingLine class should contain the following fields:
   * price
   * title 
   */ 
    
    protected  $price;
    protected $title;
    
    public function setPrice($price) {
        $this->price = $price;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
   
}
