<?php
// A simple example of creating an order from the command line.
// Run using php sample.php

# Use here the 'shop domain' of your account in Riskified
$domain = "busteco.com";

# Use here the 'auth token' as listed in the Riskified web app under the 'Settings' Tab
$authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";

$riskifiedUrl = "sandbox.riskified.com";
// $riskifiedUrl = "localhost:3000";

# Order
$order = new Order();
$order->id = 118;
$order->name = 'Order #111';
$order->email = 'great.customer@example.com';
$order->total_spent = '200';
$order->created_at = '2014-02-31 14:58:04';
$order->closed_at = '2014-03-31 14:58:05';
$order->currency = 'USD';
$order->updated_at ='2014-02-31 14:58:04';
$order->gateway ='mypaymentprocessor';
$order->browser_ip = '124.185.86.55';
$order->total_price = 113.23;
$order->total_discounts = 5.0;
$order->cancel_reason = 'inventory';
$order->cart_token = '1sdaf23j212';
$order->note = 'Shipped to my hotel.';
$order->referring_site = 'google.com';

# LineItems   
$lineItems = new LineItem();
$lineItems->price = 100;
$lineItems->quantity = 1;
$lineItems->title ='ACME Widget';        
$lineItems->product_id = 101;
$lineItems->sku = 'ABCD';
$order->line_items = $lineItems;

# DiscountCodes  
$discountCodes = new DiscountCode();  
$discountCodes->amount = 19.95;
$discountCodes->code = 12;
$order->discount_codes = $discountCodes;

# ShippingLines    
$shippingLines = new ShippingLine(); 
$shippingLines->price = 123.00;
$shippingLines->title ='Free'; 
$shippingLines->code = NULL; 
$order->shipping_lines = $shippingLines;

# PaymentDetais 
$paymentDetails = new PaymentDetails();   
$paymentDetails->credit_card_bin = '370002';
$paymentDetails->avs_result_code = 'Y';
$paymentDetails->cvv_result_code = 'N';
$paymentDetails->credit_card_number = 'xxxx-xxxx-xxxx-1234';
$paymentDetails->credit_card_company = 'VISA';    
$order->payment_details = $paymentDetails;

# Customer  
$customer = new Customer(); 
$customer->email = 'email@address.com';
$customer->first_name = 'Firstname';
$customer->last_name ='Lastname';
$customer->id = 1233;
$customer->created_at = '2012/01/15 11:22:11';
$customer->orders_count = '6';
$customer->note = NULL;
$order->customer = $customer;

# BillingAddress    
$billingAddress = new Address();
$billingAddress->first_name = 'John';
$billingAddress->last_name = 'Doe';
$billingAddress->address1 = '108 Main Street';
$billingAddress->company = 'Kansas Computers';
$billingAddress->country = 'United States';
$billingAddress->country_code = 'US';
$billingAddress->phone = '1234567';
$billingAddress->city = 'NYC';
$billingAddress->name = 'John Doe';
$billingAddress->address2 = 'Apartment 12';
$billingAddress->province = 'New York';
$billingAddress->province_code = 'NY';
$billingAddress->zip = '64155';
$order->billing_address = $billingAddress;

# ShippingAddress  
$shippingAddress = new Address();
$shippingAddress->first_name = 'John';
$shippingAddress->last_name = 'Doe';
$shippingAddress->address1 = '108 Main Street';
$shippingAddress->company = 'Kansas Computers';
$shippingAddress->country = 'United States';
$shippingAddress->country_code = 'US';
$shippingAddress->phone = '1234567';
$shippingAddress->city = 'NYC';
$shippingAddress->name = 'John Doe';
$shippingAddress->address2 = 'Apartment 12';
$shippingAddress->province = 'New York';
$shippingAddress->province_code = 'NY';
$shippingAddress->zip = '64155';
$order->shipping_address = $shippingAddress;

    
if($order->validate()) {	
	$transport = new CurlTransport();

	# available methods:__toXml(),__toJson()
	$dataString = $order->__toJson();
	
// 	echo $dataString."\n";
	
	# Send the request
	echo("Sending request...\n");	
	$result = $transport->sendRequest($dataString, $riskifiedUrl, $domain, $authToken);
	echo("Result is $result\n");
	
	$decodedResponse = json_decode($result);
	if(isset($decodedResponse->order)) {
		$orderId = $decodedResponse->order->id;
		$status = $decodedResponse->order->status;
		echo("Order $orderId status is $status\n");
	}	
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
