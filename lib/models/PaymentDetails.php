<?php namespace Riskified\SDK {
    class PaymentDetails extends AbstractModel {

        protected $_fields = [
            'credit_card_bin' => 'string',
            'avs_result_code' => 'string',
            'cvv_result_code' => 'string',
            'credit_card_number' => 'string',
            'credit_card_company' => 'string'
        ];
    }
}