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
    
    $data = array();

    // This information should reside in the OrderInfo class.
    $orderInfo = new OrderInfo();
    /*
    $data['id']                     = 118;
    $data['name']                   = 'Order #111';
    $data['email']                  = "great.customer@example.com";
    $data['total_spent']            = 200;
    $data['cancel_reason']          = 'inventory';
    $data['created_at']             = "2013-10-13 14:58:04";
    $data['closed_at']              = "2013-10-13 14:58:04";
    $data['currency']               = "USD";
    $data['updated_at']             = "2013-10-13 14:58:04";
    $data['gateway']                = "mypaymentprocessor";
    $data['browser_ip']             = "124.185.86.55";
    $data['cart_token']             = '1sdaf23j212'; # required
    $data['note']                   = "Shipped to my hotel.";
    $data['referring_site']         = "google.com";
    $data['total_price']            = 113.23;
    $data['total_discounts']        = 5;
    $data['discount_codes']         = "Black Friday";
     */
    
    // All line items should be stored in a list of LineItems
    // line items - multiple items can be added.
    $lineItems = array();
    $lineItems[0] = new LineItem();
    $lineItems[1] = new LineItem();
    /*$data['line_items'][0]['price']      = 100;
    $data['line_items'][0]['product_id'] = 101;
    $data['line_items'][0]['quantity']   = 1;
    $data['line_items'][0]['sku']        = "ABCD";
    $data['line_items'][0]['title']      = "ACME Widget";
    
    $data['line_items'][1]['price']      = 100;
    $data['line_items'][1]['product_id'] = 101;
    $data['line_items'][1]['quantity']   = 1;
    $data['line_items'][1]['sku']        = "ABCD";
    $data['line_items'][1]['title']      = "ACME Widget";
    */
    
    //shipping details
    $shipping_lines = array()
    $spipping_lines[0] = new ShippingLine() 
    /*
    $data ['shipping_lines'][]['price']   = 10;
    $data ['shipping_lines'][]['title']   = "Overnight shipping";
     */
    
    // payment details
    $payment_details = new PaymentDetails();
    /*
    $data['payment_details']['credit_card_bin']      = "370002";
    $data['payment_details']['avs_result_code']      = "Y";
    $data['payment_details']['cvv_result_code']      = "N";
    $data['payment_details']['credit_card_number']  = "XXXX-XXXX-"."1234"; // We never store or look at full credit card numbers.
    $data['payment_details']['credit_card_company'] = "VISA";
     */
    
    $customer = new Customer();
    /*
    $data['customer']['created_at']       = "31/1/2012";
    $data['customer']['email']            = "greatcustomer@example.com";
    $data['customer']['first_name']       = "Gary";
    $data['customer']['id']               = "1211";
    $data['customer']['last_name']        = "Great";
    $data['customer']['note']             = NULL;
    $data['customer']['orders_count']     = 12;
     */
    
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
