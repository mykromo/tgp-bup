<?php

namespace humhub\modules\election\activities;

use humhub\modules\activity\components\BaseActivity;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use humhub\modules\content\models\Content;
use Yii;

class ElectionCompleted extends BaseActivity implements ConfigurableActivityInterface
{
    public $viewName = 'electionCompleted';
    public $moduleId = 'election';

    public function init()
    {
        $this->visibility = Content::VISIBILITY_PUBLIC;
        parent::init();
    }

    public function getTitle()
    {
        return Yii::t('ElectionModule.base', 'Election completed');
    }

    public function getDescription()
    {
        return Yii::t('ElectionModule.base', 'Whenever an officer election is completed in one of your spaces.');
    }
}
