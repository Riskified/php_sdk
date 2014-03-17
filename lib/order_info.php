<?php

class OrderInfo {

  /**
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
  
    protected $id;
    protected $name;
    protected $email;
    protected $totalSpent;
    protected $cancelReason;
    protected $createdAt;
    protected $closedAt;
    protected $currency;
    protected $updatedAt;
    protected $gateway;
    protected $browserIp;
    protected $cartToken;
    protected $note;
    protected $refferingSite;
    protected $totalPrice;
    protected $totalDiscounts;
    
    
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setTotalSpent($totalSpent) {
        $this->totalSpent = $totalSpent;
    }
    public function setCancelReason($cancelReason) {
        $this->cancelReasond = $cancelReason;
    }
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    public function setClosedAt($closedAt) {
        $this->closedAt = $closedAt;
    }
    public function setCcurrency($currency) {
        $this->currency = $currency;
    }
    public function setUupdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    public function setGateway($gateway) {
        $this->gateway = $gateway;
    }
    public function setBrowserIp($browserIp) {
        $this->browserIp = $browserIp;
    }
    public function setCartToken($cartToken) {
        $this->cartToken = $cartToken;
    }
    public function setNote($note) {
        $this->note = $note;
    }
    public function setRefferingSite($refferingSite) {
        $this->refferingSite = $refferingSite;
    }
    public function setTotalPrice($totalPrice) {
        $this->totalPrice = $totalPrice;
    }
    public function setTotalDiscounts($totalDiscounts) {
        $this->totalDiscounts = $totalDiscounts;
    }
    
    
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getTotalSpent() {
        return $this->totalSpent;
    }

    public function getCancelReason() {
        return $this->cancelReason;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getClosedAt() {
        return $this->closedAt;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function getGateway() {
        return $this->gateway;
    }

    public function getBrowserIp() {
        return $this->browserIp;
    }

    public function getCartToken() {
        return $this->cartToken;
    }

    public function getNote() {
        return $this->note;
    }

    public function getRefferingSite() {
        return $this->refferingSite;
    }

    public function getTotalPrice() {
        return $this->totalPrice;
    }

    public function getTotalDiscounts() {
        return $this->totalDiscounts;
    }
    
}

