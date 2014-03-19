<?php
    
    //header('Content-Type: text/xml');
    
    
    // A simple example of creating an order from the command line.
    // Run using php command_line.php
    
    if(defined('STDIN') )
    echo("Running from CLI\n");
    else
    echo("Not Running from CLI");
    # Use here the 'shop domain' of your account in Riskified
    $domain = "<YOUR SHOP DOMAIN>";
    
    # Use here the 'auth token' as listed in the Riskified web app under the 'Settings' Tab
    $auth_token = "<YOUR AUTH TOKEN>";
    
    $riskified_url = "localhost:3000";
    
    echo ("Riskified URL is $riskified_url\n");
    
    
    
    // This information should reside in the OrderInfo class.
    $orderInfo = new OrderInfo();
    
    // echo "<pre>";
    // print_r(get_class_methods($orderInfo));
    // echo "</pre>";
    
    $orderInfo->setId(118);
    $orderInfo->setName('Order #111');
    $orderInfo->setEmail('great.customer@example.com');
    $orderInfo->setTotalSpent('200');
    $orderInfo->setCancelReason('inventory');
    $orderInfo->setCreatedAt('2013-10-13 14:58:04');
    $orderInfo->setClosedAt('2013-10-13 14:58:04');
    $orderInfo->setCurrency('USD');
    $orderInfo->setUpdatedAt('2013-10-13 14:58:04');
    $orderInfo->setGateway('mypaymentprocessor');
    $orderInfo->setBrowserip('124.185.86.55');
    $orderInfo->setCartToken('1sdaf23j212');
    $orderInfo->setNote('Shipped to my hotel.');
    $orderInfo->setRefferingSite('google.com');
    $orderInfo->setTotalPrice(113.23);
    $orderInfo->setTotalDiscounts(5);
    
   
    
    // All line items should be stored in a list of LineItems
    // line items - multiple items can be added.
    $lineItems = array();
    $lineItems = new LineItem();
    //$lineItems[1] = new LineItem();
    
    $lineItems->setPrice(100);
    $lineItems->setProductId(101);
    $lineItems->setQuantity(1);
    $lineItems->setSku('ABCD');
    $lineItems->setTitle('ACME Widget');
    $lineItems->setPublicUrl('publicurl.com');
    
    
    
    //discount codes
    $discountCode = new DiscountCode();
    
    $discountCode->setCode(12);
    $discountCode->setAmount('19.95');
    
    
    
    //shipping details
    $shippingLines = new ShippingLine(); 
    
    $shippingLines->setPrice(10);
    $shippingLines->setTitle('Overnight shipping'); 
    
    
    
    // payment details
    $paymentDetails = new PaymentDetails();
    
    $paymentDetails->setCreditCardBin('370002');
    $paymentDetails->setAvsResultCode('Y');
    $paymentDetails->setCvvResultCode('N');
    $paymentDetails->setCreditCardNumber('xxxx-xxxx-xxxx-1234');
    $paymentDetails->setCreditCardCompany('VISA');
    
    
    
    // customer details
    $customer = new Customer();
    
    $customer->setCreatedAt('31/1/2012');
    $customer->setEmail('email@address.com');
    $customer->setFirstName('Firstname');
    $customer->setLastName('Lastname');
    $customer->setId(1233);
    $customer->setNote(NULL);
    $customer->setOrdersCount(6);    
    
    
    
    //billing info
    $billingAddress = new Address();
    
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
  
    
    // Shipping address
    $shippingAddress  = new Address();
    
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
    
    

    $order = new Order();
    $order->setOrderInfo( $orderInfo );
    $order->setLineItems( $lineItems );
    $order->setShippingLines( $shippingLines );
    $order->setPaymentDetails( $paymentDetails );
    $order->setCustomer( $customer );
    $order->setShippingAddress( $shippingAddress );
    $order->setBillingAddress( $billingAddress );
    $order->setDiscountCodes( $discountCode );

    /*
     * available methods:
     * __toXml
     * __toJson
     */ 
    $data_string = $order->__toJson();
    echo "<pre>";  
    print_r($data_string);
    
    die();

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
                                               
                                               # use this header to force immidiate submission
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
