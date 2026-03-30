<?php

namespace humhub\modules\reactions;

use humhub\components\Module as BaseModule;
use humhub\modules\content\components\ContentActiveRecord;
use Yii;

class Module extends BaseModule
{
    public $resourcesPath = 'resources';

    public const REACTIONS = [
        'like'  => '👍',
        'love'  => '❤️',
        'haha'  => '😂',
        'wow'   => '😮',
        'sad'   => '😢',
        'angry' => '😡',
    ];

    public function getName()
    {
        return Yii::t('ReactionsModule.base', 'Emoji Reactions');
    }

    public function getDescription()
    {
        return Yii::t('ReactionsModule.base', 'Facebook-style emoji reactions for posts and comments');
    }
}
