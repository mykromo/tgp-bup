<?php
use humhub\modules\admin\widgets\AdminMenu;
use yii\helpers\Url;

AdminMenu::markAsActive('shop');
$actionId = $this->context->action->id;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('ShopModule.base', '<strong>Shop</strong> Administration') ?>
    </div>
    <div class="tab-menu">
        <ul class="nav nav-tabs">
            <li <?= $actionId === 'index' ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/index']) ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li <?= in_array($actionId, ['applications', 'review', 'approve', 'reject']) ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/applications']) ?>"><i class="fa fa-file-text fa-fw"></i> Applications</a>
            </li>
            <li <?= in_array($actionId, ['stores', 'disable-store', 'enable-store']) ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/stores']) ?>"><i class="fa fa-shopping-bag fa-fw"></i> Stores</a>
            </li>
            <li <?= $actionId === 'products' ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/products']) ?>"><i class="fa fa-cube fa-fw"></i> Products</a>
            </li>
            <li <?= in_array($actionId, ['orders', 'view-order', 'verify-order', 'cancel-order']) ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/orders']) ?>"><i class="fa fa-list fa-fw"></i> Orders</a>
            </li>
            <li <?= $actionId === 'settings' ? 'class="active"' : '' ?>>
                <a href="<?= Url::to(['/shop/admin/settings']) ?>"><i class="fa fa-cog fa-fw"></i> Settings</a>
            </li>
        </ul>
    </div>
    <?= $content ?>
</div>
