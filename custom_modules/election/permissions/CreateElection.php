<?php

namespace humhub\modules\election\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use Yii;

class CreateElection extends BasePermission
{
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
    ];

    protected $fixedGroups = [
        Space::USERGROUP_USER,
        Space::USERGROUP_GUEST,
    ];

    protected $moduleId = 'election';

    public function getTitle()
    {
        return Yii::t('ElectionModule.base', 'Create Election');
    }

    public function getDescription()
    {
        return Yii::t('ElectionModule.base', 'Can create officer election polls in this chapter');
    }
}
