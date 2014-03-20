<?php

class Customer extends Base{

  /**
   * 
   * REQUIRED
   * 
   * The Customer class should contain the following fields:
   * created_at
   * email
   * first_name
   * last_name
   * id
   * note
   * orders_count
   */ 
   
    protected $created_at;           #optional - date
    protected $email;                #required - string
    protected $first_name;           #required - string
    protected $last_name;            #required - string
    protected $id;                   #required - string
    protected $note;                 #optional - string
    protected $orders_count;         #optional - number
    //protected $verified_email;     #???????   optional - boolean   -- appear in integration_spec
   
    
    public function setCreatedAt($createdAt) {
        if($createdAt) {
            if($this->validateDate($createdAt))
            {
                $this->created_at = $createdAt;
            }
            else 
                throw new Exception('The Customer - Created At field is in wrong format.');
        }
        $this->created_at = $createdAt;
    }
    public function setEmail($email) {
        if(!$email)
            throw new Exception('The Customer - Email field is required.');
        else    
            $this->email = $email;
    }
    public function setFirstName($firstName) {
        if(!$firstName)
            throw new Exception('The Customer - First Name field is required.');
        else
            $this->first_name = $firstName;
    }
    public function setLastName($lastName) {
        if(!$lastName)
            throw new Exception('The Customer - Last Name field is required.');
        else
            $this->last_name = $lastName;
    }
    public function setId($id) {
        if(!$id)
            throw new Exception('The Customer - Id field is required.');
        else
            $this->id = $id;
    }
    public function setNote($note) {
        $this->note = $note;
    }
    public function setOrdersCount($ordersCount) {
        
        if($ordersCount) {
            if(!is_numeric($ordersCount))
                throw new Exception('The Customer - OrdersCount is not numeric.'); 
        }
        
        $this->orders_count = $ordersCount;
    }
    
    
    
    
    public function getCreatedAt() {
        return $this->created_at;   
    }
    public function getEmail() {
        return $this->email;   
    }
    public function getFirstName() {
        return $this->first_name;   
    }
    public function getlastName() {
        return $this->last_name;   
    }
    public function getId() {
        return $this->id;   
    }
    public function getNote() {
        return $this->note;   
    }
    public function getOrdersCount() {
        return $this->orders_count;   
    }
    
    
   
   
}
