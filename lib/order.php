<?php

class Order {

  /**
   * The parent class the contains the other subclasses.
   */

    public $orderInfo;       // Order Info class.
    public $customer;         // Customer class.
    public $shippingAddress; // Address class.
    public $billingAddress;  // Address class.
    public $lineItemArray;  // A list of LineItem.
    public $paymentInfo;     // PaymentDetails class.
    public $discountCodes;   // A list of DiscountCode objects
    public $shippingLines;   // A list of ShippingLine objects
    
    
    
    public function setOrderInfo( OrderInfo $orderInfo ) {
        
    }
    
    public function setLineItems() {
        
    }
    
    public function setShippingLines( ShippingLine $shippingLines) {
        
    }
    
    public function setPaymentDetails( PaymentDetails $paymentInfo) {
        
    }
    
    public function setCustomer( Customer  $customer) {
        
    }
    
    public function setShippingAddress( Address  $shippingAddress) {
        
    }
    
    public function setBillingAddress( Address  $billingAddress) {
        
    }
    
    
    
}

