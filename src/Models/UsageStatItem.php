<?php

namespace Nullform\TGStatClient\Models;

/**
 * The base class for the elements from the statistics of requests to the API.
 *
 * @see https://api.tgstat.ru/docs/ru/usage/stat.html
 */
abstract class UsageStatItem extends AbstractModel
{
    /**
     * ID of your tariff.
     *
     * @var string
     * @example api_stat_m
     */
    public $serviceKey = '';

    /**
     * The name of your tariff.
     *
     * @var string
     * @example Доступ к Stat API (тариф M)
     */
    public $title = '';

    /**
     * Number of requests expended.
     *
     * @var string
     * @example 2/100000
     */
    public $spentRequests = '';

    /**
     * Validity of the service package (unix timestamp).
     *
     * @var int
     * @example 1575158400
     */
    public $expiredAt = 0;
}
