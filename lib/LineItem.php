<?php
class LineItem extends Base{

	protected $_fields = array(
		'price' => 'string', 
		'product_id' => '',
		'quantity' => 'number',
		'sku' => '',
		'title' => 'string',
		'public_url' => ''
	);

}
?>