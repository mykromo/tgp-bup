<?php

namespace humhub\modules\stewardship\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use Yii;

class ManageFinances extends BasePermission
{
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
    ];

    protected $fixedGroups = [
        Space::USERGROUP_MEMBER,
        Space::USERGROUP_USER,
        Space::USERGROUP_GUEST,
    ];

    protected $moduleId = 'stewardship';

    public function getTitle()
    {
        return Yii::t('StewardshipModule.base', 'Manage Finances');
    }

    public function getDescription()
    {
        return Yii::t('StewardshipModule.base', 'Can create and manage funds, transactions, grants, and generate reports');
    }
}
