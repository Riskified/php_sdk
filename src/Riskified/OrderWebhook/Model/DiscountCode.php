<?php namespace Riskified\OrderWebhook\Model;
class DiscountCode extends AbstractModel {

    protected $_fields = [
        'code' => 'string',
        'amount' => 'float'
    ];
}