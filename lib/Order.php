<?php
class Order extends Base{

  /**
   * The parent class the contains the other subclasses.
   */
  
  
  
    protected $id;               #required - string
    protected $name;             #???
    protected $email;            #required - string  
    protected $total_spent;      # ----------------------  not appear in integration_spec !!! ------------------
    protected $cancel_reason;    #optional - string
    protected $created_at;       #required - date
    protected $closed_at;        #optional - date
    protected $currency;         #required - string
    protected $updated_at;       #required - date
    protected $gateway;          #required - string
    protected $browser_ip;       #required - string
    protected $cart_token;       #optional - string
    protected $note;             #???
    protected $reffering_site;   # ----------------------  not appear in integration_spec !!! ------------------
    protected $total_price;      #required - float
    protected $total_discounts;  #optional - float

    protected $customer;          # Customer class
    protected $shipping_address;  # Address class
    protected $billing_address;   # Address class
    protected $line_items;        # A list of LineItem
    protected $payment_details;   # PaymentDetails class
    protected $discount_codes;    # A list of DiscountCode objects
    protected $shipping_lines;    # A list of ShippingLine objects
    protected $isSet = false;
    
    
    
    public function setId($id) {
        if(!$id)
            throw new Exception('The Order Info - Id - is required field.');
        else    
            $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setEmail($email) {
        if(!$email)
            throw new Exception('The Order Info - Email - is required field.');
        else
            $this->email = $email;
    }
    public function setTotalSpent($total_spent) {
        $this->total_spent = $total_spent;
    }
    
    public function setCancelReason($cancel_reason) {
        $this->cancel_reason = $cancel_reason;
    }
    
    public function setCreatedAt($createdAt) {
        if(!$createdAt)
            throw new Exception('The Order - Created At - is required field.');
        else{
            if($this->validateDate($createdAt))
            {
                $this->created_at = $createdAt;
            }
            else 
                throw new Exception('The Order - Created At field is in wrong format.');
        }
    }
    
    public function setClosedAt($closed_at) {
        if($closed_at) {
            
            if($this->validateDate($closed_at))
            {
                $this->closed_at = $closed_at;
            }
            else 
                throw new Exception('The Order - Closed At field is in wrong format.');
        }
        $this->closed_at = $closed_at;
    }
    
    public function setCurrency($currency) {
        if(!$currency)
            throw new Exception('The Order - Currency - is required field.');
        else
            $this->currency = $currency;
    }
    
    public function setUpdatedAt($updated_at) {
        if(!$updated_at)
            throw new Exception('The Order - Updated At - is required field.');
        else {
                    
                if($this->validateDate($updated_at))
                {
                    $this->updated_at = $updated_at;
                }
                else 
                    throw new Exception('The Order - Updated At field is in wrong format.');            

        }
    }
    
    public function setGateway($gateway) {
        if(!$gateway)
            throw new Exception('The Order - Gateway - is required field.');
        else
            $this->gateway = $gateway;
    }

    public function setBrowserIp($browser_ip) {
        if(!$browser_ip)
            throw new Exception('The Order - Browser Ip - is required field.');
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
            throw new Exception('The Order - Total Price - is required field.');
        else {
            if(!is_float($total_price))
                throw new Exception('The Order - Total Price - field is not float.');
            else
                $this->total_price = $total_price;
        }
    }

    public function setTotalDiscounts($total_discounts) {
        if($total_discounts) {
            if(!is_float($total_discounts))
                throw new Exception('The Order - Total Discounts - field is not float.');
            else
                $this->total_discounts = $total_discounts;
        }
        else 
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
    
    
    
    public function setLineItems($lineItems) {
        $this->line_items = $lineItems;
        $this->isSet = true;
    }
    
    public function setShippingLines( $shippingLines ) {
        $this->shipping_lines = $shippingLines;
        $this->isSet = true;
    }
    
    public function setPaymentDetails( $paymentDetails ) {
        $this->payment_details = $paymentDetails;
    }
    
    public function setDiscountCodes( $discountCodes ) {
        $this->discount_codes = $discountCodes;
        $this->isSet = true;
    }
    
    public function setCustomer( $customer ) {
        $this->customer = $customer;
        $this->isSet = true;
    }
    
    public function setShippingAddress( $shippingAddress ) {
        $this->shipping_address = $shippingAddress;
        $this->isSet = true;
    }
    
    public function setBillingAddress( $billingAddress ) {
        $this->billing_address = $billingAddress;
        $this->isSet = true;
    }
    
    
    public function isValid() {
        
        return $this->isSet;
        
    }
    
}

