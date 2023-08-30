<?php

namespace Nullform\TGStatClient;

use Nullform\TGStatClient\Params\AbstractParams;

/**
 * TGStat API request.
 */
class Request
{
    /**
     * GET or POST.
     *
     * @var string
     */
    public $method;

    /**
     * Base URL.
     *
     * @var string
     */
    public $base_url;

    /**
     * Request path.
     *
     * Example: posts/search
     *
     * @var string
     */
    public $path;

    /**
     * Request parameters.
     *
     * @var Params\AbstractParams
     */
    public $params;

    /**
     * Sandbox mode.
     *
     * @var bool
     * @see Client::sandbox()
     */
    public $sandbox = false;

    /**
     * User-Agent.
     *
     * @var string
     * @see Client::userAgent()
     */
    public $user_agent = '';

    /**
     * Request timeout.
     *
     * @var int
     * @see Client::timeout()
     */
    public $timeout = 0;

    /**
     * Request body.
     *
     * @var string
     */
    public $body = '';


    /**
     * 32 byte hash of Request object.
     *
     * @return string
     */
    public function hash(): string
    {
        return md5(json_encode($this));
    }

    /**
     * Send HTTP request.
     *
     * @return Response
     * @uses curl_exec()
     */
    public function send(): Response
    {
        $response = new Response();
        $request_body = '';
        $response_body = '';
        $endpoint = $this->base_url . '/' . $this->path;
        $headers = [];

        $curl_options = [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_USERAGENT      => $this->user_agent,
        ];

        if ($this->method === 'POST' || $this->method === 'PUT') {

            $curl_options[CURLOPT_CUSTOMREQUEST] = $this->method;

            if ($this->method === 'POST') {
                $curl_options[CURLOPT_POST] = true;
            }

            if (!empty($this->params)) {
                $request_body = $this->params->toJson();
                $headers[] = 'Content-Type: application/json';
            }

        } else {

            $params_string = $this->params instanceof AbstractParams ? $this->params->toString() : '';
            $curl_options[CURLOPT_URL] = $endpoint . (!empty($params_string) ? '?' . $params_string : '');

        }

        if (!empty($request_body)) {
            $curl_options[CURLOPT_POSTFIELDS] = $request_body;
            $headers[] = 'Content-Length: ' . strlen($request_body);
        }

        $curl_options[CURLOPT_HTTPHEADER] = $headers;

        $ch = curl_init();

        curl_setopt_array($ch, $curl_options);

        $response_body = (string)curl_exec($ch);

        $curl_info = curl_getinfo($ch);

        $response->setResponseBody($response_body);
        $response->setHttpStatus((int)$curl_info['http_code']);

        return $response;
    }
}
