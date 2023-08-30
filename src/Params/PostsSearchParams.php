<?php

namespace Nullform\TGStatClient\Params;

/**
 * Parameters for finding publications.
 *
 * @see https://api.tgstat.ru/docs/ru/posts/search.html
 */
class PostsSearchParams extends AbstractParams
{
    public const MAX_LIMIT = 50;
    public const MAX_OFFSET = 1000;

    /**
     * The text to search.
     *
     * @var string
     */
    public $q;

    /**
     * The number of search results to return.
     *
     * Default 20, max 50.
     *
     * @var int
     */
    public $limit;

    /**
     * The offset required to fetch a specific subset of the search results.
     *
     * Default 0, max 1000.
     *
     * @var int
     */
    public $offset;

    /**
     * Type of source (channel, chat, all).
     *
     * Default: all.
     *
     * @var string
     */
    public $peerType;

    /**
     * Publication date from (timestamp).
     *
     * @var int
     */
    public $startDate;

    /**
     * Date published by (timestamp).
     *
     * @var int
     */
    public $endDate;

    /**
     * Hide reposts from search results.
     *
     * Default: 0.
     *
     * @var int
     */
    public $hideForwards;

    /**
     * Hide deleted posts.
     *
     * Default: 0.
     *
     * @var int
     */
    public $hideDeleted;

    /**
     * Enable strict search (disables morphology and search by part of a word).
     *
     * Default: 0.
     *
     * @var int
     */
    public $strongSearch;

    /**
     * List minus words (separator - space).
     *
     * @var string
     */
    public $minusWords;

    /**
     * Whether the request uses advanced query syntax.
     *
     * Default: 0.
     *
     * @var int
     * @see https://api.tgstat.ru/docs/ru/extended-syntax.html
     */
    public $extendedSyntax;

    /**
     * The response will return a Channel objects with information about the channels in which publications were found.
     *
     * Default: 0.
     *
     * @var
     */
    public $extended;
}
