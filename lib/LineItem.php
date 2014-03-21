<?php namespace riskified\sdk {
class LineItem extends Base {

	protected $_fields = array(
		'price' => 'string', 
		'product_id' => 'string optional',
		'quantity' => 'number',
		'sku' => 'string optional',
		'title' => 'string',
		'public_url' => 'string optional'
	);

}
}?>