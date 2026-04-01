<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
$isNew = $model->isNewRecord;
$this->title = $isNew ? Yii::t('StewardshipModule.base', 'Add Category') : Yii::t('StewardshipModule.base', 'Edit Category');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?= $this->title ?></strong></div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <?php if ($isNew): ?>
            <?= $form->field($model, 'key')->textInput(['maxlength' => 50])
                ->hint(Yii::t('StewardshipModule.base', 'Unique identifier (lowercase, no spaces). Example: community_outreach')) ?>
        <?php else: ?>
            <div class="form-group">
                <label><?= Yii::t('StewardshipModule.base', 'Key') ?></label>
                <p class="form-control-static"><code><?= Html::encode($model->key) ?></code></p>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'label')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'sort_order')->input('number', ['min' => 0])
            ->hint(Yii::t('StewardshipModule.base', 'Lower numbers appear first.')) ?>
        <hr>
        <?= Html::submitButton($isNew ? Yii::t('StewardshipModule.base', 'Add Category') : Yii::t('StewardshipModule.base', 'Save'), ['class' => 'btn btn-primary']) ?>
        <a href="<?= $cc->createUrl('/stewardship/category/index') ?>" class="btn btn-default"><?= Yii::t('StewardshipModule.base', 'Cancel') ?></a>
        <?php ActiveForm::end(); ?>
    </div>
</div>
