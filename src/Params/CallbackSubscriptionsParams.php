<?php

namespace Nullform\TGStatClient\Params;

use Nullform\TGStatClient\Models\Subscription;

/**
 * Parameters for subscriptions list.
 *
 * @package Nullform\TGStatClient
 */
class CallbackSubscriptionsParams extends AbstractParams
{
    /**
     * Subscription ID.
     *
     * @var int|null
     */
    public $subscription_id;

    /**
     * Subscription type.
     *
     * @var string|null
     * @see Subscription::TYPE_CHANNEL
     * @see Subscription::TYPE_KEYWORD
     */
    public $subscription_type;
}