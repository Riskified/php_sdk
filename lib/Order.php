<?php
class Order extends Base{

  protected $_fields = array(
	'id' => 'string', 
  	'name' => '', 
  	'email' => 'string', 
  	'total_spent' => '', 
  	'cancel_reason' => '', 
  	'created_at' => 'date', 
  	'closed_at' => '', 
  	'currency' => 'string', 
  	'updated_at' => 'date', 
  	'gateway' => 'string', 
  	'browser_ip' => 'string', 
  	'cart_token' => '', 
  	'note' => '',
  	'referring_site' => '',
	'total_price' => 'number',		
	'total_discounts' => '',
	'customer' => 'object',
	'shipping_address' => 'object',
	'billing_address' => 'object',
	'line_items' => 'object',
	'payment_details' => 'object',
	'discount_codes' => 'object',
	'shipping_lines' => 'object'						
  );
   
}
?>