<?php

class LineItem extends Base{

  /** 
   * 
   * REQUIRED 
   *
   * 
   * The LineItem class should contain the following fields:
   * price
   * product_id
   * quantity
   * sku
   * title
   * public_url
   */ 
   
    protected $price;       #required - string
    protected $product_id;   #optional - string
    protected $quantity;    #required - string
    protected $sku;         #optional - string
    protected $title;       #required - string
    protected $public_url;   # ----------------------  not appear in integration_spec !!! ------------------
    
    
    public function setPrice($price) {
        if(!$price)
            throw new Exception('The LineItem - Price field is required');
        else    
            $this->price = $price;
    }
    
    public function setProductId($productId) {
        $this->product_id = $productId;
    }
    
    public function setQuantity($quantity) {
        if(!$quantity)
            throw new Exception('The LineItem - Quantity field is required');
        else
            $this->quantity = $quantity;
    }
    
    public function setSku($sku) {
        $this->sku = $sku;
    }
    
    public function setTitle($title) {
        if(!$title)
            throw new Exception('The LineItem - Title field is required');
        else
            $this->title = $title;
    }
    
    public function setPublicUrl($publicUrl) {
        $this->public_url = $publicUrl;
    }
    
    
    public function getPrice() {
        return $this->price;
    }
    public function getProductId() {
        return $this->product_id;
    }
    public function getQuantity() {
        return $this->quantity;
    }
    public function getSku() {
        return $this->sku;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getPublicUrl() {
        return $this->public_url;
    }
    
  

}
