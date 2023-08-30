<?php

namespace Nullform\TGStatClient\Models;

/**
 * Access statistics for Callback API.
 */
class UsageStatItemCallback extends UsageStatItem
{
    /**
     * The number of subscriptions according to your tariff.
     *
     * @var string
     */
    public $spentObjects = '';
}
