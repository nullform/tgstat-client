<?php

namespace Nullform\TGStatClient\Models;

/**
 * User's Telegram profile.
 *
 * @package Nullform\TGStatClient\Models
 */
class TelegramProfile extends AbstractModel
{
    /**
     * Telegram ID.
     *
     * @var int
     */
    public $id;

    /**
     * Telegram username.
     *
     * @var string
     */
    public $username;

    /**
     * First name.
     *
     * @var string
     */
    public $first_name;

    /**
     * Last name.
     *
     * @var string
     */
    public $last_name;

    /**
     * Link to user photo.
     *
     * @var string
     */
    public $photo;

    /**
     * @var string
     */
    public $about;

    /**
     * @var int
     */
    public $last_online;
}