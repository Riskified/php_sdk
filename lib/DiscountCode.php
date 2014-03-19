<?php

class DiscountCode extends Base{

  /**
   * The Discount code class should contain the following fields:
   * code
   * amount
   */
  
    protected $code;
    protected $amount;
    
    
    public function setCode($code) {
        $this->code = $code;
    }
    
    public function setAmount($amount) {
        $this->amount = $amount;
    }
    
    
    public function getCode() {
        return $this->code;
    } 
      
    public function getAmount() {
        return $this->amount;
    }
    
      
}
