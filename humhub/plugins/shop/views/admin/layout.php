<?php
use humhub\modules\admin\widgets\AdminMenu;
use humhub\widgets\FooterMenu;

AdminMenu::markAsActive('shop');
?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?= AdminMenu::widget(); ?>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t('ShopModule.base', '<strong>Shop</strong> Administration') ?>
                </div>
                <?= $content ?>
            </div>
            <?= FooterMenu::widget(); ?>
        </div>
    </div>
</div>
