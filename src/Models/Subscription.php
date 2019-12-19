<?php

namespace Nullform\TGStatClient\Models;

/**
 * Events subscriptions.
 *
 * @package Nullform\TGStatClient
 * @see https://api.beta.tgstat.ru/docs/ru/callback/subscriptions-list.html
 */
class Subscription extends AbstractModel
{
    /**
     * Channel subscription.
     */
    public const TYPE_CHANNEL = 'channel';
    /**
     * Keyword subscription.
     */
    public const TYPE_KEYWORD = 'keyword';

    /**
     * Subscription id.
     *
     * @var int
     */
    public $subscription_id;

    /**
     * Event types.
     *
     * @var string[]
     * @see Event::TYPE_NEW_POST
     * @see Event::TYPE_EDIT_POST
     * @see Event::TYPE_REMOVE_POST
     */
    public $event_types = [];

    /**
     * Subscription type.
     *
     * @var string
     */
    public $type;

    /**
     * @var Channel|null
     */
    public $channel;

    /**
     * @var Keyword|null
     */
    public $keyword;

    /**
     * Subscription creation date (timestamp).
     *
     * @var int
     */
    public $created_at;

    /**
     * @inheritDoc
     */
    public function fill(?\stdClass $obj): void
    {
        parent::fill($obj);

        if ($this->type == static::TYPE_CHANNEL) {
            if (!empty($obj->channel)) {
                $this->channel = new Channel($obj->channel);
            }
            $this->keyword = null;
        } elseif ($this->type == static::TYPE_KEYWORD) {
            if (!empty($obj->keyword)) {
                $this->keyword = new Keyword($obj->keyword);
            }
            $this->channel = null;
        }
    }
}