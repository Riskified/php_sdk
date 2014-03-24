<?php namespace Riskified\DecisionNotification\Model;

use Riskified\DecisionNotification\Exception;

class Notification {

    public $id;
    public $status;
    protected $signature;
    protected $headers;
    protected $headers_map;
    protected $body;

    public function __construct($signature, $headers, $body) {
        $this->signature = $signature;
        $this->headers = $headers;
        $this->parse_headers();
        $this->body = $body;
        $this->parse_body($body);
        $this->test_authorization();
    }

    protected function parse_headers() {
        $this->headers_map = [];
        foreach($this->headers as $i => $line) {
            list ($key, $value) = explode(':', $line);
            if (!$key || !$value)
                throw new Exception\BadHeaderException($this->headers, $this->body);
            $this->headers_map[$key] = $value;
        }
    }

    protected function test_authorization() {
        $signature = $this->signature;
        $remote_hmac = $this->headers_map[$signature::HMAC_HEADER_NAME];
        $local_hmac = $signature->calc_hmac($this->data_string());
        if ($remote_hmac != $local_hmac)
            throw new Exception\AuthorizationException($this->headers, $this->body);
    }

    protected function parse_body() {
        $vars = [];
        parse_str($this->body, $vars);
        if (!$vars['id'] || !$vars['status'])
            throw new Exception\BadPostParametersException($this->headers, $this->body);

        $this->id = $vars['id'];
        $this->status = $vars['status'];
    }

    protected function data_string() {
        return "id=$this->id&status=$this->status";
    }
}