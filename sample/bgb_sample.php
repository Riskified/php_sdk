<?php
    
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
    $orderInfo->setReferringSite('google.com');
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
    
    
    
    //shipping details
    $spipping_lines = new ShippingLine(); 
    
    $spipping_lines->setPrice(10);
    $spipping_lines->setTitle('Overnight shipping'); 
    
    
    
    // payment details
    $payment_details = new PaymentDetails();
    
    $payment_details->setCreditCardBin('370002');
    $payment_details->setAvsResultCode('Y');
    $payment_details->setCvvResultCode('N');
    $payment_details->setCreditCardNumber('xxxx-xxxx-xxxx-1234');
    $payment_details->setCreditCardCompany('VISA');
    
    
    
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
    $billing_address = new Address();
    /*
    $data['billing_address']['first_name']    = 'Gary';
    $data['billing_address']['last_name']     = 'Great';
    $data['billing_address']['name']          = "Gary Great"; // Can also be a formula such as first name + last name
    $data['billing_address']['address1']      = '108 Main Street';
    $data['billing_address']['address2']      = 'Apartment 12';
    $data['billing_address']['company']       = "Kansas Computers";
    $data['billing_address']['country']       = 'United States';
    $data['billing_address']['country_code']  = 'US';
    $data['billing_address']['phone']         = '12345345';
    $data['billing_address']['province']      = 'New York';
    $data['billing_address']['province_code'] = 'NY';
    $data['billing_address']['city']          = 'NYC';
    $data['billing_address']['zip']           = '64155';
     */
    
    // Shipping address
    $shipping_address  = new Address();
    /*
    $data['shipping_address']['first_name']    = 'Gary';
    $data['shipping_address']['last_name']     = 'Great';
    $data['shipping_address']['name']          = "Gary Great"; // Can also be a formula such as first name + last name
    $data['shipping_address']['address1']      = '108 Main Street';
    $data['shipping_address']['address2']      = 'Apartment 12';
    $data['shipping_address']['company']       = "Kansas Computers";
    $data['shipping_address']['country']       = 'United States';
    $data['shipping_address']['country_code']  = 'US';
    $data['shipping_address']['phone']         = '12345345';
    $data['shipping_address']['city']          = 'NYC';
    $data['shipping_address']['province']      = 'New York';
    $data['shipping_address']['province_code'] = 'NY';
    $data['shipping_address']['zip']           = '64155';
     */

    $order = new Order();
    $order->setOrderInfo( $orderInfo );
    $order->setLineItems( $line_items );
    $order->setShippingLines( $shipping_lines );
    $order->setPaymentDetails( $payment_details );
    $order->setCustomer( $customer );
    $order->setShippingAddress( $orderInfo );
    $order->setBillingAddress( $orderInfo );

    if ($order->isValid())
    {
      submit_now = false;
      $decodedRespons = eorder->send( $auth_token, $submit_now );
      if(isset($decodedResponse->order))
      {
        $orderId = $decodedResponse->order->id;
        $status = $decodedResponse->order->status;
        echo("Order $orderId status is $status\n");
      }
    }
    
?>
