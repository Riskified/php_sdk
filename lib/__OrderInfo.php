<?php
class OrderInfo extends Base{

  /**
   * 
   * REQUIRED 
   * 
   *
   * The OrderInfo class should contain the following fields:
   * id
   * name
   * email
   * total_spent
   * cancel_reason
   * created_at
   * closed_at
   * currency
   * updated_at
   * gateway
   * browser_ip
   * cart_token
   * note
   * referring_site
   * total_price
   * total_discounts
   */
  
    protected $id;              #required - string
    protected $name;            #???
    protected $email;           #required - string  
    protected $total_spent;      # ----------------------  not appear in integration_spec !!! ------------------
    protected $cancel_reason;    #optional - string
    protected $created_at;       #required - date
    protected $closed_at;        #optional - date
    protected $currency;        #required - string
    protected $updated_at;       #required - date
    protected $gateway;         #required - string
    protected $browser_ip;       #required - string
    protected $cart_token;       #optional - string
    protected $note;            #???
    protected $reffering_site;   # ----------------------  not appear in integration_spec !!! ------------------
    protected $total_price;      #required - float
    protected $total_discounts;  #optional - float
    
    
    public function setId($id) {
        if(!$id)
            throw new Exception('The Order Info - Id - is required field');
        else    
            $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setEmail($email) {
        if(!$email)
            throw new Exception('The Order Info - Email - is required field');
        else
            $this->email = $email;
    }
    public function setTotalSpent($total_spent) {
        $this->total_spent = $total_spent;
    }
    
    public function setCancelReason($cancel_reason) {
        $this->cancel_reason = $cancel_reason;
    }
    
    public function setCreatedAt($created_at) {
        if(!$created_at)
            throw new Exception('The Order Info - Created At - is required field');
        else
        $this->created_at = $created_at;
    }
    
    public function setClosedAt($closed_at) {
        $this->closed_at = $closed_at;
    }
    
    public function setCurrency($currency) {
        if(!$currency)
            throw new Exception('The Order Info - Currency - is required field');
        else
            $this->currency = $currency;
    }
    public function setUpdatedAt($updated_at) {
        if(!$updated_at)
            throw new Exception('The Order Info - Updated At - is required field');
        else
            $this->updated_at = $updated_at;
    }
    public function setGateway($gateway) {
        if(!$gateway)
            throw new Exception('The Order Info - Gateway - is required field');
        else
            $this->gateway = $gateway;
    }
    public function setBrowserIp($browser_ip) {
        if(!$browser_ip)
            throw new Exception('The Order Info - Browser Ip - is required field');
        else
            $this->browser_ip = $browser_ip;
    }
    public function setCartToken($cart_token) {
        $this->cart_token = $cart_token;
    }
    public function setNote($note) {
        $this->note = $note;
    }               
    public function setRefferingSite($reffering_site) {
        $this->reffering_site = $reffering_site;
    }
    public function setTotalPrice($total_price) {
        if(!$total_price)
            throw new Exception('The Order Info - Total Price - is required field');
        else
        $this->total_price = $total_price;
    }
    public function setTotalDiscounts($total_discounts) {
        $this->total_discounts = $total_discounts;
    }
    
    
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getTotalSpent() {
        return $this->total_spent;
    }

    public function getCancelReason() {
        return $this->cancel_reason;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getClosedAt() {
        return $this->closed_at;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function getGateway() {
        return $this->gateway;
    }

    public function getBrowserIp() {
        return $this->browser_ip;
    }

    public function getCartToken() {
        return $this->cart_token;
    }

    public function getNote() {
        return $this->note;
    }

    public function getRefferingSite() {
        return $this->reffering_site;
    }

    public function getTotalPrice() {
        return $this->total_price;
    }

    public function getTotalDiscounts() {
        return $this->total_discounts;
    }
    
   
    
}

