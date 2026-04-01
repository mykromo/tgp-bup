<?php

namespace humhub\modules\stewardship\helpers;

use Yii;

class Currency
{
    public static function format($amount): string
    {
        return '₱' . number_format((float) $amount, 2);
    }
}
