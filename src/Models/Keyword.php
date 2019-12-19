<?php

namespace Nullform\TGStatClient\Models;

/**
 * Subscription keyword.
 *
 * @package Nullform\TGStatClient
 * @see https://api.beta.tgstat.ru/docs/ru/callback/subscriptions-list.html
 */
class Keyword extends AbstractModel
{
    public const PEER_TYPE_CHANNEL = 'channel';
    public const PEER_TYPE_CHAT = 'chat';
    public const PEER_TYPE_ALL = 'all';

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