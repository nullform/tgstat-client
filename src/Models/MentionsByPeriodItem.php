<?php

namespace Nullform\TGStatClient\Models;

/**
 * Mentioned keywords by period.
 *
 * @see https://api.tgstat.ru/docs/ru/words/mentions-by-period.html
 */
class MentionsByPeriodItem extends AbstractModel
{
    /**
     * Example: 2018-11-04
     *
     * @var string
     */
    public $period = '';

    /**
     * Number of references.
     *
     * @var int
     */
    public $mentions_count = 0;

    /**
     * Number of views.
     *
     * @var int
     */
    public $views_count = 0;
}
