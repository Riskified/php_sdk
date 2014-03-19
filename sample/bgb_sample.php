<?php
    
    //header('Content-Type: text/xml');
    
    
    // A simple example of creating an order from the command line.
    // Run using php command_line.php
    
    // if(defined('STDIN') )
        // echo("Running from CLI\n");
    // else
        // echo("Not Running from CLI");
    
    # Use here the 'shop domain' of your account in Riskified
    $domain = "busteco.com";
    
    # Use here the 'auth token' as listed in the Riskified web app under the 'Settings' Tab
    $auth_token = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";
    
    $riskified_url = "sandbox.riskified.com";
    
    
    $errors = array();
    $order = new Order();
    $lineItems = new LineItem();
    $discountCode = new DiscountCode();
    $shippingLines = new ShippingLine(); 
    $paymentDetails = new PaymentDetails();
    $customer = new Customer();
    $billingAddress = new Address();
    $shippingAddress  = new Address();
    

# OrderInfo    
    try {
        $order->setId(118);
        $order->setName('Order #111');
        $order->setEmail('great.customer@example.com');
        $order->setTotalSpent('200');
        $order->setCancelReason('inventory');
        $order->setCreatedAt('2014-03-32 14:58:04');
        $order->setClosedAt('2014-03-19 14:58:05');
        $order->setCurrency('USD');
        $order->setUpdatedAt('2014-10-13 14:58:04');
        $order->setGateway('mypaymentprocessor');
        $order->setBrowserip('124.185.86.55');
        $order->setCartToken('1sdaf23j212');
        $order->setNote('Shipped to my hotel.');
        $order->setRefferingSite('google.com');
        $order->setTotalPrice(113.23);
        $order->setTotalDiscounts(5);
    }
    catch(Exception $e) {
        //echo 'Caught exception in OrderInfo: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# LineItems   
    try {
        $lineItems->setPrice(100);
        $lineItems->setProductId(101);
        $lineItems->setQuantity(1);
        $lineItems->setSku('ABCD');
        $lineItems->setTitle('ACME Widget');
        $lineItems->setPublicUrl('publicurl.com');
    }
    catch(Exception $e) {
        //echo 'Caught exception in LineItems: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# DiscountCodes    
    try {
        $discountCode->setCode(12);
        $discountCode->setAmount('19.95');
    }
    catch(Exception $e) {
        //echo 'Caught exception in DiscountCodes: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# ShippingLines    
    try {
        $shippingLines->setPrice('123');
        $shippingLines->setTitle('Free'); 
    }
    catch(Exception $e) {
        //echo 'Caught exception in ShippingLines: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# PaymentDetais    
    try {
        $paymentDetails->setCreditCardBin('370002');
        $paymentDetails->setAvsResultCode('Y');
        $paymentDetails->setCvvResultCode('N');
        $paymentDetails->setCreditCardNumber('xxxx-xxxx-xxxx-1234');
        $paymentDetails->setCreditCardCompany('VISA');
    }
    catch(Exception $e) {
        //echo 'Caught exception in PaymentDetails: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# Customer   
    try {
        $customer->setCreatedAt('31/1/2012');
        $customer->setEmail('email@address.com');
        $customer->setFirstName('Firstname');
        $customer->setLastName('Lastname');
        $customer->setId(1233);
        $customer->setNote(NULL);
        $customer->setOrdersCount(6);    
    }
    catch(Exception $e) {
        //echo 'Caught exception in Customer: '.$e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
    
    
# BillingAddress    
    try {
        $billingAddress->setFirstName('John');
        $billingAddress->setLastName('Doe');
        $billingAddress->setName('John Doe');
        $billingAddress->setAddressOne('108 Main Street');
        $billingAddress->setAddressTwo('Apartment 12');
        $billingAddress->setCompany('Kansas Computers');
        $billingAddress->setCountry('United States');
        $billingAddress->setCountryCode('US');
        $billingAddress->setPhone('1234567');
        $billingAddress->setProvince('New York');
        $billingAddress->setProvinceCode('NY');
        $billingAddress->setCity('NYCity');
        $billingAddress->setZip('64155');
        
    }
    catch(Exception $e) {
        //echo 'Caught exception in BillingAddress: '. $e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }
  

# ShippingAddress    
    try {
        $shippingAddress->setFirstName('John');
        $shippingAddress->setLastName('Doe');
        $shippingAddress->setName('John Doe');
        $shippingAddress->setAddressOne('108 Main Street');
        $shippingAddress->setAddressTwo('Apartment 12');
        $shippingAddress->setCompany('Kansas Computers');
        $shippingAddress->setCountry('United States');
        $shippingAddress->setCountryCode('US');
        $shippingAddress->setPhone('1234567');
        $shippingAddress->setProvince('New York');
        $shippingAddress->setProvinceCode('NY');
        $shippingAddress->setCity('NYCity');
        $shippingAddress->setZip('64155');
    }
    catch(Exception $e) {
        //echo 'Caught exception ShippingAddress: '. $e->getMessage()."\n";
        $errors[] = $e->getMessage();
    }

// echo "<pre>";
// print_r($errors);

if(empty($errors)) {
    # ORDER
      
    try {
        $order->setLineItems( $lineItems );
        $order->setShippingLines( $shippingLines );
        $order->setPaymentDetails( $paymentDetails );
        $order->setCustomer( $customer );
        $order->setShippingAddress( $shippingAddress );
        $order->setBillingAddress( $billingAddress );
        $order->setDiscountCodes( $discountCode );
    }
    catch(Exception $e) {
        echo $e->getMessage()."\n";
        return;
    }
    
    if($order->isValid()) {
        
        $data_string = '';
        # available methods:__toXml(),__toJson()
        $data_string = $order->__toJson();
        
        //echo $data_string."<br>";
        $hash_code = hash_hmac('sha256', $data_string, $auth_token);
        
        // Send the request
        $ch = curl_init("http://$riskified_url/webhooks/merchant_order_created");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json',
                                                   'Content-Length: ' . strlen($data_string),
                                                   'X_RISKIFIED_SHOP_DOMAIN:'.$domain,
                                                   'X_RISKIFIED_SUBMIT_NOW:true',
                                                   'X_RISKIFIED_HMAC_SHA256:'.$hash_code)
                    );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        echo("Sending request...\n");
        $result = curl_exec($ch);
        echo("Result is $result\n");
        
        $decodedResponse = json_decode($result);
        if(isset($decodedResponse->order))
        {
            $orderId = $decodedResponse->order->id;
            $status = $decodedResponse->order->status;
            echo("Order $orderId status is $status\n");
        }
        
        
    }

    

}



// load classes from /lib
function __autoload($class_name) {
    $classPath = '../lib/'.$class_name . '.php';    
    if(file_exists($classPath)) {
        require($classPath);   
    } else {
        throw new Exception("Unable to load class $class_name from $classPath");
    }
}
    
?>
