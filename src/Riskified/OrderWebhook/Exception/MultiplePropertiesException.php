<?php namespace Riskified\OrderWebhook\Exception;

use Riskified\Common\Exception\BaseException;

class MultiplePropertiesException extends BaseException {

    protected $exceptions;

    public function __construct($exceptions) {
        $this->exceptions = $exceptions;
    }

    public function __toString() {
        $sep = PHP_EOL.' ';
        return get_class($this).': '.$sep.join($sep, $this->exceptions);
    }

} 