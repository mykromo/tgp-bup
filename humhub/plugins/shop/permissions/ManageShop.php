<?php

namespace humhub\modules\shop\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use Yii;

class ManageShop extends BasePermission
{
    public $defaultAllowedGroups = [Space::USERGROUP_OWNER, Space::USERGROUP_ADMIN];
    protected $fixedGroups = [Space::USERGROUP_MEMBER, Space::USERGROUP_USER, Space::USERGROUP_GUEST];
    protected $moduleId = 'shop';

    public function getTitle() { return Yii::t('ShopModule.base', 'Manage Shop'); }
    public function getDescription() { return Yii::t('ShopModule.base', 'Can manage products, orders, and payment settings'); }
}
