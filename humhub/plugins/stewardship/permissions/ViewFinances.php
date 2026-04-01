<?php

namespace humhub\modules\stewardship\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use Yii;

class ViewFinances extends BasePermission
{
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
    ];

    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
    ];

    protected $moduleId = 'stewardship';

    public function getTitle()
    {
        return Yii::t('StewardshipModule.base', 'View Finances');
    }

    public function getDescription()
    {
        return Yii::t('StewardshipModule.base', 'Can view financial reports and fund balances');
    }
}
