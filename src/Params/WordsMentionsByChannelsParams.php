<?php

namespace Nullform\TGStatClient\Params;

/**
 * Parameters for finding words mentions in the context of channels.
 *
 * @package Nullform\TGStatClient\Params
 */
class WordsMentionsByChannelsParams extends AbstractParams
{
    /**
     * Keyword.
     *
     * @var string
     */
    public $q;

    /**
     * Source type (channel, chat, all).
     *
     * @var string
     */
    public $peerType;

    /**
     * Mention period from (timestamp).
     *
     * @var int
     */
    public $startDate;

    /**
     * Mention period to (timestamp).
     *
     * @var int
     */
    public $endDate;

    /**
     * Exclude mentions in publications that are reposts.
     *
     * @var int
     */
    public $hideForwards;

    /**
     * Enable strict search (disables morphology and search by part of a word).
     *
     * @var int
     */
    public $strongSearch;

    /**
     * Minus words (separator - space).
     *
     * @var string
     */
    public $minusWords;

    /**
     * Whether the request uses extended query syntax.
     *
     * @var int
     * @see https://api.tgstat.ru/docs/ru/extended-syntax.html
     */
    public $extendedSyntax;
}