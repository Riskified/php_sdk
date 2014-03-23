<?php namespace Riskified\SDK {
    class LineItem extends AbstractModel {

        protected $_fields = [
            'price' => 'float',
            'quantity' => 'number',
            'title' => 'string',
            'sku' => 'string optional',
            'product_id' => 'string optional'
        ];
    }
}