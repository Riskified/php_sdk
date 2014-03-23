<?php namespace Riskified\OrderWebhook\Model;
class PaymentDetails extends AbstractModel {

    protected $_fields = [
        'credit_card_bin' => 'string',
        'avs_result_code' => 'string /^[A-Z]$/i',
        'cvv_result_code' => 'string /^[A-Z]?$/i',
        'credit_card_number' => 'string',
        'credit_card_company' => 'string'
    ];
}