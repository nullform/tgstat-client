<?php

namespace Nullform\TGStatClient\Models;

/**
 * Callback URL information.
 *
 * @package Nullform\TGStatClient
 * @see https://api.beta.tgstat.ru/docs/ru/callback/get-callback-info.html
 */
class CallbackInfo extends AbstractModel
{
    /**
     * Callback URL.
     *
     * @var string
     */
    public $url;

    /**
     * Number of messages in the send queue (0 - all messages are sent, the queue is empty).
     *
     * @var int
     */
    public $pending_update_count;

    /**
     * Date of the last error that occurred while sending (null - if there were no errors).
     *
     * @var int|null
     */
    public $last_error_date;

    /**
     * The text of the last error that occurred when sending the error (null - if there were no errors).
     *
     * @var string|null
     */
    public $last_error_message;
}