<?php namespace Riskified\OrderWebhook\Transport;

use Riskified\OrderWebhook\Exception;
/**
 * Class CurlTransport
 * @package Riskified
 */
class CurlTransport extends AbstractTransport {

    /**
     * @var int
     */
    public $timeout = 10;
    public $dns_cache = true;

    /**
     * @param $order
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\CurlException
     */
    protected function send_json_request($order) {
        $data_string = $order->toJson();

        $ch = curl_init($this->full_path());
        $options = [
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers($data_string),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_DNS_USE_GLOBAL_CACHE => $this->dns_cache
        ];
        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        if (curl_errno($ch))
            throw new Exception\CurlException(curl_errno($ch), curl_error($ch));

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->json_response($body, $status);
    }

    /**
     * @param $body
     * @param $status
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\MalformedJsonException
     */
    private function json_response($body, $status) {
        $response = json_decode($body);
        if (!$response)
            throw new Exception\MalformedJsonException($body, $status);

        return $response;
    }
}