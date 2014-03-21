<?php namespace riskified\sdk {
class Order extends Base {

	protected $_fields = array(
		'id' => 'string', 
		'name' => 'string optional', 
		'email' => 'string', 
		'total_spent' => 'number optional', 
		'cancel_reason' => 'string optional', 
		'created_at' => 'date', 
		'closed_at' => 'date optional', 
		'currency' => 'string', 
		'updated_at' => 'date', 
		'gateway' => 'string', 
		'browser_ip' => 'string', 
		'cart_token' => 'string optional', 
		'note' => 'string optional',
		'referring_site' => 'string optional',
		'total_price' => 'number',		
		'total_discounts' => 'number optional',
		'customer' => 'object',
		'shipping_address' => 'object',
		'billing_address' => 'object',
		'payment_details' => 'object',
		'line_items' => 'objects',
		'discount_codes' => 'objects optional',
		'shipping_lines' => 'objects optional'						
	);
   
}
} ?>