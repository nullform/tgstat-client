<?php

namespace Nullform\TGStatClient\Models;

/**
 * Search API access statistics.
 */
class UsageStatItemSearch extends UsageStatItem
{
    /**
     * The number of unique keywords for which requests were made and the allowed number of unique keywords,
     * according to your tariff.
     *
     * @var string
     * @example 651/1000
     */
    public $spentWords = '';
}
