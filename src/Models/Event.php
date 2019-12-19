<?php

namespace Nullform\TGStatClient\Models;

/**
 * API Callback events.
 *
 * @package Nullform\TGStatClient
 * @see https://api.beta.tgstat.ru/docs/ru/objects/CallbackEvents.html
 */
class Event extends AbstractModel
{
    /**
     * New post.
     */
    public const TYPE_NEW_POST = 'new_post';
    /**
     * Edit post.
     */
    public const TYPE_EDIT_POST = 'edit_post';
    /**
     * Remove post.
     */
    public const TYPE_REMOVE_POST = 'remove_post';

    /**
     * @var int
     */
    public $subscription_id;

    /**
     * @var string
     * @see Subscription::TYPE_CHANNEL
     * @see Subscription::TYPE_KEYWORD
     */
    public $subscription_type;

    /**
     * @var int
     */
    public $event_id;

    /**
     * @var string
     * @see Event::TYPE_NEW_POST
     * @see Event::TYPE_EDIT_POST
     * @see Event::TYPE_REMOVE_POST
     */
    public $event_type;

    /**
     * @var Post
     */
    public $post;

    /**
     * @var Channel[]
     */
    public $channels = [];
}