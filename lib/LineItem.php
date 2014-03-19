<?php

class LineItem extends Base{

  /** 
   * The LineItem class should contain the following fields:
   * price
   * product_id
   * quantity
   * sku
   * title
   * public_url
   */ 
   
    protected $price;
    protected $productId;
    protected $quantity;
    protected $sku;
    protected $title;
    protected $publicUrl;
    
    
    public function setPrice($price) {
        $this->price = $price;
    }
    public function setProductId($productId) {
        $this->productId = $productId;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
    public function setSku($sku) {
        $this->sku = $sku;
    }
    public function setTitle($title) {
        $this->title = $title;
    }
    public function setPublicUrl($publicUrl) {
        $this->publicUrl = $publicUrl;
    }
    
    
    public function getPrice() {
        return $this->price;
    }
    public function getProductId() {
        return $this->productId;
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
        return $this->publicUrl;
    }
    
  

}
