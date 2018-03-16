<?php 
/**
 * Created by PhpStorm.
 * User: droritbaron
 * Date: 3/29/15
 * Time: 5:29 PM
 */

namespace Riskified\OrderWebhook\Model;

class MerchantSettings extends AbstractModel {

    protected $_fields = array(
        'settings' => 'array string'
    );
}