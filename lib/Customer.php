<?php

class Customer {

  /**
   * The Customer class should contain the following fields:
   * created_at
   * email
   * first_name
   * last_name
   * id
   * note
   * orders_count
   */ 
   
    protected $createdAt;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $id;
    protected $note;
    protected $ordersCount;
   
    
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setNote($note) {
        $this->note = $note;
    }
    public function setOrdersCount($ordersCount) {
        $this->ordersCount = $ordersCount;
    }
    
    
    
    
    public function getCreatedAt() {
        return $this->createdAt;   
    }
    public function getEmail() {
        return $this->email;   
    }
    public function getFirstName() {
        return $this->firstName;   
    }
    public function getlastName() {
        return $this->lastName;   
    }
    public function getId() {
        return $this->id;   
    }
    public function getNote() {
        return $this->note;   
    }
    public function getOrdersCount() {
        return $this->ordersCount;   
    }
    
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
        // If the property isn't an object or array, leave it untouched
        return $array;
    }
   
    public function __toString() {
        return json_encode($this->toArray());
    }
   
   
}
