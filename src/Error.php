<?php

namespace Nullform\TGStatClient;

/**
 * TGStat API error.
 *
 * @package Nullform\TGStatClient
 * @see https://api.tgstat.ru/docs/ru/errors.html
 */
class Error
{
    /**
     * @var string
     */
    public $error_id = '';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var array
     */
    protected $errors = [
        'empty_token'            => 'No token provided',
        'token_invalid'          => 'Invalid token provided',
        'wrong_method'           => 'Invalid method',
        'wrong_method_type'      => 'Invalid request type for method',
        'flood_control_10'       => 'API calls too frequent (10)',
        'flood_control_60'       => 'API calls too frequent (60)',
        'no_active_subscription' => 'API service subscription not found',
        'quota_requests_reached' => 'Quota on total number of requests per month exceeded',
        'quota_channel_reached'  => 'The quota for the number of unique channels per month has been exceeded',
        'quota_keywords_reached' => 'The quota for the number of Unique keywords per month has been exceeded',
        'quota_foreign_channel'  => 'On free tariff, you can request data only for channels that have passed the owner confirmation',
        'invalid_phone'          => 'Invalid phone',
        'unknown_error'          => 'Unknown error',
    ];

    /**
     * @param string $error_id Example: unknown_error
     */
    public function __construct(string $error_id)
    {
        if ($this->find($error_id)) {
            $this->error_id = $error_id;
            $this->message = $this->find($error_id);
        }
    }

    /**
     * Find error by its error_id.
     *
     * @param string $error_id
     * @return string|null
     */
    protected function find(string $error_id): ?string
    {
        $error = null;

        if (isset($this->errors[$error_id])) {
            $error = $this->errors[$error_id];
        }

        return $error;
    }
}