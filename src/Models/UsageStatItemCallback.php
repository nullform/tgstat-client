<?php

namespace Nullform\TGStatClient\Models;

/**
 * Access statistics for Callback API.
 *
 * @package Nullform\TGStatClient
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