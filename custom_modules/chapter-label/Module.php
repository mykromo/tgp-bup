<?php

namespace humhub\modules\chapterlabel;

use humhub\components\Module as BaseModule;

class Module extends BaseModule
{
    public function getName()
    {
        return 'Chapter Label';
    }

    public function getDescription()
    {
        return 'Renames all Space/Spaces labels to Chapter/Chapters system-wide';
    }
}
