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
    $authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";
    
    $riskifiedUrl = "sandbox.riskified.com";
    
    
    $errors = array();
    $order = new Order();
    $lineItems = new LineItem();
    $discountCode = new DiscountCode();
    $shippingLines = new ShippingLine(); 
    $paymentDetails = new PaymentDetails();
    $customer = new Customer();
    $billingAddress = new Address();
    $shippingAddress  = new Address();
    $transport = new Transport();
    

# OrderInfo    
    try {
        $order->setId(118);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $order->setName('Order #111');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $order->setEmail('great.customer@example.com');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    try {
        $order->setTotalSpent('200');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    try {
        $order->setCreatedAt('2014-02-31 14:58:04');
    }
    catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    try {
        $order->setClosedAt('2014-03-31 14:58:05');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    try {
        $order->setCurrency('USD');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    try {
        $order->setUpdatedAt('2014-02-31 14:58:04');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }    
    try {
        $order->setGateway('mypaymentprocessor');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }    
    try {
        $order->setBrowserip('124.185.86.55');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }    
    try {
        $order->setTotalPrice(113.23);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }    
    try {
       $order->setTotalDiscounts(5.0);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }    
        
    $order->setCancelReason('inventory');
    $order->setCartToken('1sdaf23j212');
    $order->setNote('Shipped to my hotel.');
    $order->setRefferingSite('google.com');
    
    
    
   
# LineItems   
    try {
        $lineItems->setPrice(100);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    } 
    try {
        $lineItems->setQuantity(1);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    } 
    try {
        $lineItems->setTitle('ACME Widget');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    } 
        
    $lineItems->setProductId(101);
    $lineItems->setSku('ABCD');
        
   
    
    
# DiscountCodes    
    try {
        $discountCode->setAmount(19.95);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    $discountCode->setCode(12);
    
    
    
# ShippingLines    
    try {
        $shippingLines->setPrice(123.00);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingLines->setTitle('Free'); 
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    $shippingLines->setCode(NULL); 
    
    
# PaymentDetais    
    try {
        $paymentDetails->setCreditCardBin('370002');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $paymentDetails->setAvsResultCode('Y');
        
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $paymentDetails->setCvvResultCode('N');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $paymentDetails->setCreditCardNumber('xxxx-xxxx-xxxx-1234');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $paymentDetails->setCreditCardCompany('VISA');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    
# Customer   
    try {
        $customer->setEmail('email@address.com');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $customer->setFirstName('Firstname');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $customer->setLastName('Lastname');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $customer->setId(1233);
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $customer->setCreatedAt('2012/01/15 11:22:11');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $customer->setOrdersCount('6');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    
    $customer->setNote(NULL);
    
    
# BillingAddress    
    try {
        $billingAddress->setFirstName('John');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setLastName('Doe');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setAddressOne('108 Main Street');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setCompany('Kansas Computers');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setCountry('United States');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setCountryCode('US');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setPhone('1234567');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $billingAddress->setCity('NYCity');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
  
        $billingAddress->setName('John Doe');
        $billingAddress->setAddressTwo('Apartment 12');
        $billingAddress->setProvince('New York');
        $billingAddress->setProvinceCode('NY');
        $billingAddress->setZip('64155');

# ShippingAddress    
    try {
        $shippingAddress->setFirstName('John');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setLastName('Doe');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setAddressOne('108 Main Street');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setCountry('United States');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setCountryCode('US');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setPhone('1234567');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
    try {
        $shippingAddress->setCity('NYCity');
    }catch(Exception $e) {
        $errors[] = $e->getMessage();
    }

        $shippingAddress->setName('John Doe');
        $shippingAddress->setAddressTwo('Apartment 12');
        $shippingAddress->setCompany('Kansas Computers');
        $shippingAddress->setProvince('New York');
        $shippingAddress->setProvinceCode('NY');
        $shippingAddress->setZip('64155');



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
        
        $dataString = '';
        # available methods:__toXml(),__toJson()
        $dataString = $order->__toJson();
        
        # echo $data_string."<br>";
        
        # Send the request
        $result = $transport->sendRequest($dataString, $riskifiedUrl, $domain, $authToken);
        echo("Sending request...\n");
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
else {
    echo "<ul>";
        foreach($errors as $err) {
            echo "<li>". $err. "</li>";
        }
    echo "</ul>";
	
}


# load classes from /lib
function __autoload($class_name) {
    $classPath = '../lib/'.$class_name . '.php';    
    if(file_exists($classPath)) {
        require($classPath);   
    } else {
        throw new Exception("Unable to load class $class_name from $classPath");
    }
}
    
?>
