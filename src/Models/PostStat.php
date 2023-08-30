<?php

namespace Nullform\TGStatClient\Models;

/**
 * Publication statistics.
 *
 * @see https://api.tgstat.ru/docs/ru/posts/stat.html
 */
class PostStat extends AbstractModel
{
    /**
     * Number of views at the time of the request.
     *
     * @var int
     */
    public $viewsCount = 0;

    /**
     * Number of reposts of the publication.
     *
     * @var int
     */
    public $forwardsCount = 0;

    /**
     * The number of times the publication was mentioned using a link like t.me/username/123.
     *
     * @var int
     */
    public $mentionsCount = 0;

    /**
     * Array with a list of reposts.
     *
     * @var PostForward[]
     */
    public $forwards = [];

    /**
     * @inheritDoc
     */
    public function fill(?\stdClass $obj): void
    {
        parent::fill($obj);

        $this->viewsCount = (int)$obj->viewsCount;
        $this->forwardsCount = (int)$obj->forwardsCount;
        $this->mentionsCount = (int)$obj->mentionsCount;

        if (!empty($obj->forwards) && is_array($obj->forwards)) {
            foreach ($obj->forwards as $forward) {
                $this->forwards[] = new PostForward($forward);
            }
        }
    }
}
