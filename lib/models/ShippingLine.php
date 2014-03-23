<?php namespace Riskified\SDK {
    class ShippingLine extends AbstractModel {

        protected $_fields = [
            'price' => 'float',
            'title' => 'string',
            'code' => 'string optional'
        ];
    }
}