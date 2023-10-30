<?php

namespace Nullform\TGStatClient\Params;

class WordsMentionsByPeriodParams extends WordsMentionsParams
{
    /**
     * Grouping results (day, week, month).
     *
     * @var string
     */
    public $group = 'day';
}
