<?php
class Customer extends Base{

	protected $_fields = array(
		'created_at' => 'date', 
		'email' => 'string', 
		'first_name' => 'string', 
		'last_name' => 'string', 
		'id' => 'string', 
		'note' => '', 
		'orders_count' => 'number' 
		// verified_email => ''
	);
	
}
?>