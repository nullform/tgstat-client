<?php

namespace Nullform\TGStatClient\Params;

use Nullform\TGStatClient\Models\Event;

/**
 * Channel subscription parameters.
 *
 * @see https://api.beta.tgstat.ru/docs/ru/callback/subscribe-channel.html
 */
class CallbackSubscribeChannelParams extends AbstractParams
{
    /**
     * Subscription ID.
     *
     * If passed, the subscription with the specified ID will be edited (instead of adding new one).
     *
     * @var int|null
     */
    public $subscription_id;

    /**
     * Channel ID (@username, t.me/username, t.me/joinchat/AAAAABbbbbbcccc ... or channel ID in TGStat).
     *
     * @var string|int
     */
    public $channel_id;

    /**
     * Types of events to be notified (separated by commas).
     *
     * @var string
     * @see Event::TYPE_NEW_POST
     * @see Event::TYPE_EDIT_POST
     * @see Event::TYPE_REMOVE_POST
     */
    public $event_types;
}
