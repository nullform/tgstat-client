<?php

namespace Nullform\TGStatClient\Models;

/**
 * Information about the Telegram channel.
 *
 * @see https://api.tgstat.ru/docs/ru/objects/Channel.html
 */
class Channel extends AbstractModel
{
    /**
     * Internal channel ID in TGStat.
     *
     * @var int
     */
    public $id = 0;

    /**
     * Link to the channel in Telegram.
     *
     * @var string
     */
    public $link = '';

    /**
     * Channel username - @username.
     *
     * @var string
     */
    public $username = '';

    /**
     * The name of the channel.
     *
     * @var string
     */
    public $title = '';

    /**
     * Channel description.
     *
     * @var string
     */
    public $about = '';

    /**
     * 100px channel picture.
     *
     * @var string
     */
    public $image100 = '';

    /**
     * 640px channel picture.
     *
     * @var string
     */
    public $image640 = '';

    /**
     * The number of channel subscribers at the time of the request.
     *
     * @var int
     */
    public $participants_count = 0;
}
