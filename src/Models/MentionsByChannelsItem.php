<?php

namespace Nullform\TGStatClient\Models;

/**
 * Mentioned keywords by channel.
 *
 * @package Nullform\TGStatClient
 * @see https://api.tgstat.ru/docs/ru/words/mentions-by-channels.html
 */
class MentionsByChannelsItem extends AbstractModel
{
    /**
     * Channel ID.
     *
     * @var int
     */
    public $channel_id = 0;

    /**
     * Number of references.
     *
     * @var int
     */
    public $mentions_count = 0;

    /**
     * Number of views.
     *
     * @var int
     */
    public $views_count = 0;

    /**
     * Date of last mention (unix timestamp).
     *
     * @var int
     */
    public $last_mention_date = 0;

    /**
     * Telegram channel.
     *
     * @var Channel
     */
    public $channel;


    /**
     * @inheritDoc
     */
    public function fill(?\stdClass $obj): void
    {
        parent::fill($obj);

        if (!empty($obj->channel) && $obj->channel instanceof \stdClass) {
            $this->channel = new Channel($obj->channel);
        }
    }
}