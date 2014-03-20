<?php
class PaymentDetails extends Base{

	protected $_fields = array(
		'credit_card_bin' => 'string', 
		'avs_result_code' => 'string', 
		'cvv_result_code' => 'string', 
		'credit_card_number' => 'string', 
		'credit_card_company' => 'string'
	);

}
?>