<?php namespace Riskified\OrderWebhook\Exception;

use Riskified\Common\Exception\BaseException;

class MalformedJsonException extends BaseException {

    public function __construct($body, $status) {

    }

}