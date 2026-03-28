<?php

namespace humhub\modules\election;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\election\models\Election;
use humhub\modules\space\models\Space;

class Module extends ContentContainerModule
{
    public $controllerNamespace = 'humhub\modules\election\controllers';

    public function getContentContainerTypes()
    {
        return [Space::class];
    }

    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [
                new permissions\CreateElection(),
            ];
        }
        return [];
    }

    public function getContentClasses(?ContentContainerActiveRecord $contentContainer = null): array
    {
        return [Election::class];
    }

    public function getName()
    {
        return \Yii::t('ElectionModule.base', 'Officer Election');
    }

    public function getDescription()
    {
        return \Yii::t('ElectionModule.base', 'Allows chapter members to vote for chapter officers via polls');
    }
}
