<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\onlineUsers\models\Config;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var Config $config */
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('OnlineUsersModule.base', '<strong>Online</strong> users') ?></div>

    <div class="panel-body">
        <h4><?= Yii::t('OnlineUsersModule.base', 'Settings') ?></h4>
        <div class="help-block">
            <?= Yii::t('OnlineUsersModule.base', 'General settings for the Online Users module.') ?>
        </div>

        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($config, 'limit')->textInput(['type' => 'number']) ?>
        <?= $form->field($config, 'sidebarOrder')->textInput(['type' => 'number']) ?>

        <?= Button::save()->submit() ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
