<?php

namespace humhub\modules\stewardship;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use Yii;

class Module extends ContentContainerModule
{
    public $controllerNamespace = 'humhub\modules\stewardship\controllers';

    public function init()
    {
        parent::init();
        Yii::setAlias('@stewardship', $this->getBasePath());
    }

    public function getContentContainerTypes()
    {
        return [Space::class];
    }

    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [
                new permissions\ManageFinances(),
                new permissions\ViewFinances(),
            ];
        }
        return [];
    }

    public function getName()
    {
        return Yii::t('StewardshipModule.base', 'Financial Stewardship');
    }

    public function getDescription()
    {
        return Yii::t('StewardshipModule.base', 'Non-profit fund accounting, grant reporting, and audit compliance');
    }
}
