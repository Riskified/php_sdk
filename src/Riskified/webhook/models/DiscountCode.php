<?php namespace Riskified;
class DiscountCode extends AbstractModel {

    protected $_fields = [
        'code' => 'string',
        'amount' => 'float'
    ];
}