<?php

namespace Nullform\TGStatClient;

use Nullform\TGStatClient\Params\AbstractParams;

/**
 * TGStat API request.
 *
 * @package Nullform\TGStatClient
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
        $body = '';
        $params_string = $this->params instanceof AbstractParams ? $this->params->toString() : '';
        $endpoint = $this->base_url . '/' . $this->path;

        $curl_options = [
            CURLOPT_URL            => $endpoint . (!empty($params_string) ? '?' . $params_string : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_USERAGENT      => $this->user_agent,
        ];

        if ($this->method === 'POST') {
            $curl_options[CURLOPT_POST] = true;
        }

        $ch = curl_init();

        curl_setopt_array($ch, $curl_options);

        $body = (string)curl_exec($ch);

        $curl_info = curl_getinfo($ch);

        $response->setResponseBody($body);
        $response->setHttpStatus((int)$curl_info['http_code']);

        return $response;
    }
}