<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Transaction;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
$isNew = $model->isNewRecord;
$this->title = $isNew ? Yii::t('StewardshipModule.base', 'Record Transaction') : Yii::t('StewardshipModule.base', 'Edit Transaction');
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?= $this->title ?></strong></div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'fund_id')->dropDownList(
            ArrayHelper::map($funds, 'id', function($f) { return $f->name . ' (' . $f->fund_type . ')'; }),
            ['prompt' => '— Select Fund —']
        ) ?>
        <?= $form->field($model, 'type')->dropDownList(Transaction::getTypeLabels(), ['prompt' => '— Select —']) ?>
        <?= $form->field($model, 'amount')->input('number', ['step' => '0.01', 'min' => '0.01']) ?>
        <?= $form->field($model, 'description')->textInput(['maxlength' => 500]) ?>
        <?= $form->field($model, 'transaction_date')->input('date') ?>
        <?= $form->field($model, 'functional_category')->dropDownList($categories, ['prompt' => '— Optional —']) ?>
        <?= $form->field($model, 'program_name')->textInput(['maxlength' => 255])
            ->hint(Yii::t('StewardshipModule.base', 'Required for program expenses. Must match the fund restriction purpose for restricted funds.')) ?>
        <?= $form->field($model, 'grant_id')->dropDownList(
            ArrayHelper::map($grants, 'id', 'name'),
            ['prompt' => '— No Grant —']
        ) ?>
        <?= $form->field($model, 'reference')->textInput(['maxlength' => 100])
            ->hint(Yii::t('StewardshipModule.base', 'Invoice #, check #, or receipt reference')) ?>
        <hr>
        <?= Html::submitButton($isNew ? Yii::t('StewardshipModule.base', 'Record Transaction') : Yii::t('StewardshipModule.base', 'Save Changes'), ['class' => 'btn btn-primary']) ?>
        <a href="<?= $contentContainer->createUrl($isNew ? '/stewardship/dashboard/index' : '/stewardship/transaction/ledger') ?>" class="btn btn-default"><?= Yii::t('StewardshipModule.base', 'Cancel') ?></a>
        <?php ActiveForm::end(); ?>
    </div>
</div>
