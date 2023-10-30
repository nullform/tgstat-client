<?php

namespace Nullform\TGStatClient\Models;

/**
 * Telegram post item.
 *
 * @see https://api.tgstat.ru/docs/ru/objects/Post.html
 */
class Post extends AbstractModel
{
    /**
     * Publication ID in TGStat.
     *
     * @var int
     */
    public $id = 0;

    /**
     * Timestamp of publication.
     *
     * @var int
     */
    public $date = 0;

    /**
     * The number of views at the time of the request.
     *
     * @var int
     */
    public $views = 0;

    /**
     * @var int
     */
    public $shares_count = 0;

    /**
     * @var int
     */
    public $comments_count = 0;

    /**
     * @var int 
     */
    public $reactions_count = 0;

    /**
     * Telegram-link to the publication.
     *
     * @var string
     */
    public $link = '';

    /**
     * Channel ID in TGStat.
     *
     * @var int
     */
    public $channel_id = 0;

    /**
     * Channel ID in TGStat from which the repost is made. Null, if the message is not repost.
     *
     * @var int|null
     */
    public $forwarded_from;

    /**
     * Channel from which the repost is made. Null, if the message is not repost.
     *
     * @var Channel|null
     */
    public $forwarded_from_channel;

    /**
     * Is the message deleted from Telegram (1 | 0).
     *
     * @var int
     */
    public $is_deleted = 0;

    /**
     * Timestamp of the publication deletion date (null - if not deleted).
     *
     * @var null|int
     */
    public $deleted_at = null;

    /**
     * The full text of the publication.
     *
     * @var string
     */
    public $text = '';

    /**
     * The full text of the publication with the <mark> tag highlighted by the keywords found (to highlight
     * the found keywords).
     *
     * @var string
     */
    public $snippet = '';

    /**
     * An object with media content hosted in a publication.
     *
     * @var \stdClass|null
     * @see https://api.tgstat.ru/docs/ru/objects/Media.html
     */
    public $media;

    /**
     * Telegram channel.
     *
     * @var Channel|null
     */
    public $channel;


    /**
     * @inheritDoc
     */
    public function fill(?\stdClass $obj): void
    {
        parent::fill($obj);

        if (!empty($obj->channel) && $obj->channel instanceof \stdClass) {
            $this->channel = new Channel($obj->channel);
        }
        if (!empty($obj->forwarded_from_channel) && $obj->forwarded_from_channel instanceof \stdClass) {
            $this->forwarded_from_channel = new Channel($obj->forwarded_from_channel);
        }
    }
}
