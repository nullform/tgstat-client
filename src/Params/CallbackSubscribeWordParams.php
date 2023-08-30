<?php

namespace Nullform\TGStatClient\Params;

use Nullform\TGStatClient\Models\Keyword;
use Nullform\TGStatClient\Models\Event;

/**
 * Keyword subscription parameters.
 */
class CallbackSubscribeWordParams extends AbstractParams
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
     * Types of events to be notified (separated by commas).
     *
     * @var string
     * @see Event::TYPE_NEW_POST
     * @see Event::TYPE_EDIT_POST
     * @see Event::TYPE_REMOVE_POST
     */
    public $event_types;

    /**
     * Keyword / phrase.
     *
     * @var string
     */
    public $q;

    /**
     * Enable strict search (disables morphology and word search).
     *
     * @var bool
     */
    public $strong_search;

    /**
     * List of negative words (separator - space).
     *
     * @var string
     */
    public $minus_words;

    /**
     * Does the query use extended syntax?
     *
     * @var bool
     */
    public $extended_syntax;

    /**
     * Source type (channels or chats).
     *
     * @var string
     * @see Keyword::PEER_TYPE_CHANNEL
     * @see Keyword::PEER_TYPE_CHAT
     * @see Keyword::PEER_TYPE_ALL
     */
    public $peer_types;
}
