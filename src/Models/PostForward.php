<?php

namespace Nullform\TGStatClient\Models;

/**
 * One repost.
 *
 * @see https://api.tgstat.ru/docs/ru/posts/stat.html
 */
class PostForward extends AbstractModel
{
    /**
     * @var int
     */
    public $postId = 0;

    /**
     * @var string
     */
    public $postLink = "";

    /**
     * @var int
     */
    public $postDate = 0;

    /**
     * @var int
     */
    public $channelId = 0;

    /**
     * @var null|Channel
     */
    public $channel;

    /**
     * @inheritDoc
     */
    public function fill(?\stdClass $obj): void
    {
        $this->postId = (int)$obj->postId;
        $this->postLink = (string)$obj->postLink;
        $this->postDate = (int)$obj->postDate;
        $this->channelId = (int)$obj->channelId;
    }
}
