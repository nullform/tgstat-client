<?php

namespace Nullform\TGStatClient;

/**
 * TGStat API response.
 *
 * @see https://api.tgstat.ru/docs/
 */
class Response
{
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';
    public const STATUS_PENDING = 'pending';

    /**
     * Status.
     *
     * @var string
     * @see Response::STATUS_OK
     * @see Response::STATUS_ERROR
     * @see Response::STATUS_PENDING
     */
    public $status = '';

    /**
     * Restored from cache.
     *
     * @var bool
     */
    public $from_cache = false;

    /**
     * Error.
     *
     * @var Error|null
     */
    protected $error;

    /**
     * HTTP status code.
     *
     * @var int
     */
    protected $http_status = 0;

    /**
     * Response body.
     *
     * @var string
     */
    protected $body = '';


    /**
     * @param string $body Response body.
     */
    public function __construct(string $body = '')
    {
        if (!empty($body)) {
            $this->setResponseBody($body);
        }
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->http_status;
    }

    /**
     * Payload data from response body.
     *
     * @return \stdClass|array|string|null
     */
    public function getPayload()
    {
        $body_obj = @json_decode($this->body);
        $response = null;

        if (!empty($body_obj->response)) {
            $response = $body_obj->response;
        }

        return $response;
    }

    /**
     * Get response error.
     *
     * @return Error|null
     */
    public function getError(): ?Error
    {
        return $this->error;
    }

    /**
     * Set HTTP status code.
     *
     * @param int $http_status
     */
    public function setHttpStatus(int $http_status): void
    {
        $this->http_status = $http_status;
    }

    /**
     * Set response body.
     *
     * @param string $body
     */
    public function setResponseBody(string $body): void
    {
        $this->body = $body;

        if (!empty($this->body)) {
            $response = @json_decode($this->body);

            if (!empty($response->status)) {
                $this->status = $response->status;
            }
            if (!empty($response->error)) {
                $this->error = new Error($response->error);
            }
        }
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getResponseBody(): string
    {
        return $this->body;
    }
}
