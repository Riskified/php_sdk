<?php

class Order extends Base{

  /**
   * The parent class the contains the other subclasses.
   */

    public $orderInfo;       // Order Info class.
    public $customer;         // Customer class.
    public $shippingAddress; // Address class.
    public $billingAddress;  // Address class.
    public $lineItems;  // A list of LineItem.
    public $paymentDetails;     // PaymentDetails class.
    public $discountCodes;   // A list of DiscountCode objects
    public $shippingLines;   // A list of ShippingLine objects
    
    
    
    public function setOrderInfo( $orderInfo ) {
        $this->orderInfo = $orderInfo;
    }
    
    public function setLineItems($lineItems) {
        $this->lineItems = $lineItems;
    }
    
    public function setShippingLines( $shippingLines ) {
        $this->shippingLines = $shippingLines;
    }
    
    public function setPaymentDetails( $paymentDetails ) {
        $this->paymentDetails = $paymentDetails;
    }
    
    public function setDiscountCodes( $discountCodes ) {
        $this->discountCodes = $discountCodes;
    }
    
    public function setCustomer( $customer ) {
        $this->customer = $customer;
    }
    
    public function setShippingAddress( $shippingAddress ) {
        $this->shippingAddress = $shippingAddress;
    }
    
    public function setBillingAddress( $billingAddress ) {
        $this->billingAddress = $billingAddress;
    }
    
    
    
    public function isValid() {
        
        $valid = true;
        
        return $valid;
        
    }
    
}

