<?php
// A simple example of creating an order from the command line.
// Usage: php sample.php

include '../init.php';
use riskified\sdk as rs;

# Replace with the 'shop domain' of your account in Riskified
$domain = "busteco.com";

# Replace with the 'auth token' listed in the Riskified web app under the 'Settings' Tab
$authToken = "bde6c2dce1657b1197cbebb10e4423b3560a3a6b";

# Change to wh.riskified.com for production
$riskifiedUrl = "sandbox.riskified.com";


# Order
$order = new rs\Order();
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
$lineItem1 = new rs\LineItem();
$lineItem1->price = 100;
$lineItem1->quantity = 1;
$lineItem1->title ='ACME Widget';        
$lineItem1->product_id = 101;
$lineItem1->sku = 'ABCD';

$lineItem2 = new rs\LineItem();
$lineItem2->price = 200;
$lineItem2->quantity = 4;
$lineItem2->title ='ACME Spring';        
$lineItem2->product_id = 202;
$lineItem2->sku = 'BCDE';
$order->line_items = array( $lineItem1, $lineItem2 );

# DiscountCodes  
$discountCode = new rs\DiscountCode();  
$discountCode->amount = 19.95;
$discountCode->code = 12;
$order->discount_codes = $discountCode;

# ShippingLines    
$shippingLine = new rs\ShippingLine(); 
$shippingLine->price = 123.00;
$shippingLine->title ='Free'; 
$shippingLine->code = NULL; 
$order->shipping_lines = $shippingLine;

# PaymentDetais 
$paymentDetails = new rs\PaymentDetails();   
$paymentDetails->credit_card_bin = '370002';
$paymentDetails->avs_result_code = 'Y';
$paymentDetails->cvv_result_code = 'N';
$paymentDetails->credit_card_number = 'xxxx-xxxx-xxxx-1234';
$paymentDetails->credit_card_company = 'VISA';    
$order->payment_details = $paymentDetails;

# Customer  
$customer = new rs\Customer(); 
$customer->email = 'email@address.com';
$customer->first_name = 'Firstname';
$customer->last_name ='Lastname';
$customer->id = 1233;
$customer->created_at = '2012/01/15 11:22:11';
$customer->orders_count = '6';
$customer->note = NULL;
$order->customer = $customer;

# BillingAddress    
$billingAddress = new rs\Address();
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
$shippingAddress = new rs\Address();
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

// echo $order->to_json()."\n";

# Create a curl-based transport to the Riskified Server    
$transport = new rs\CurlTransport($riskifiedUrl, $domain, $authToken);

echo("Sending Request...\n");	
$response = $transport->sendRequest($order);

$json = json_encode($response, JSON_PRETTY_PRINT);
echo("Response:\n $json\n");
    
?>
