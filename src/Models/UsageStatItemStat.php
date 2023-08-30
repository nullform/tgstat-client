<?php

namespace Nullform\TGStatClient\Models;

/**
 * Stat API access statistics.
 */
class UsageStatItemStat extends UsageStatItem
{
    /**
     * The number of unique channels to which requests were made and the allowed number of unique channels,
     * according to your tariff.
     *
     * @var string
     * @example 2/500
     */
    public $spentChannels = '';
}
