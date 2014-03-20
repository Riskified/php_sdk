<?php
class Address extends Base{
  
  protected $_fields = array(
	'first_name' => 'string', 
  	'last_name' => 'string', 
  	'name' => '', 
  	'company' => 'string', 
  	'address1' => 'string', 
  	'address2' => '', 
  	'city' => 'string', 
  	'country_code' => 'string', 
  	'country' => 'string', 
  	'province_code' => '', 
  	'province' => '', 
  	'zip' => '', 
  	'phone' => 'string'
  );

}
?>