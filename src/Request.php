<?php

namespace Nullform\TGStatClient;

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
}