<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Review Application');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><?= $this->title ?></strong>
    <span class="label label-<?= Vendor::getStatusBadge($vendor->status) ?> pull-right"><?= Vendor::getStatusLabels()[$vendor->status] ?></span>
</div>
<div class="panel-body">
<div class="row">
    <div class="col-sm-6">
        <h4><?= Yii::t('ShopModule.base', 'Applicant') ?></h4>
        <div class="well">
            <?php if ($vendor->user): ?>
            <div class="media">
                <div class="media-left">
                    <img src="<?= $vendor->user->getProfileImage()->getUrl() ?>" class="img-circle" width="48" height="48">
                </div>
                <div class="media-body">
                    <h5><a href="<?= $vendor->user->getUrl() ?>"><?= Html::encode($vendor->user->displayName) ?></a></h5>
                    <p class="text-muted"><?= Html::encode($vendor->user->email) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <h4><?= Yii::t('ShopModule.base', 'Shop Details') ?></h4>
        <p><strong><?= Yii::t('ShopModule.base', 'Shop Name:') ?></strong> <?= Html::encode($vendor->shop_name) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Description:') ?></strong> <?= Html::encode($vendor->description) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Applied:') ?></strong> <?= Yii::$app->formatter->asDatetime($vendor->created_at) ?></p>
    </div>
    <div class="col-sm-6">
        <h4><?= Yii::t('ShopModule.base', 'Submitted Documents') ?></h4>
        <?php $docLabels = Vendor::getRequiredDocuments(); ?>
        <?php foreach ($vendor->documents as $doc): ?>
        <div class="well well-sm">
            <strong><?= Html::encode($docLabels[$doc->document_type] ?? $doc->document_type) ?></strong><br>
            <a href="<?= Yii::getAlias('@web') . '/' . $doc->file_path ?>" target="_blank" data-pjax-prevent>
                <i class="fa fa-file"></i> <?= Html::encode($doc->file_name) ?>
            </a>
            <small class="text-muted">(<?= Yii::$app->formatter->asShortSize($doc->file_size) ?>)</small>
        </div>
        <?php endforeach; ?>
        <?php if (empty($vendor->documents)): ?>
            <p class="text-danger">No documents uploaded.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($vendor->isPending()): ?>
<hr>
<div class="row">
    <div class="col-sm-6">
        <a href="<?= Url::to(['/shop/vendor/approve', 'id' => $vendor->id]) ?>"
           class="btn btn-success btn-block" data-method="post" data-confirm="Approve this vendor?">
            <i class="fa fa-check"></i> <?= Yii::t('ShopModule.base', 'Approve') ?>
        </a>
    </div>
    <div class="col-sm-6">
        <?= Html::beginForm(Url::to(['/shop/vendor/reject', 'id' => $vendor->id]), 'post') ?>
        <div class="input-group">
            <input type="text" name="reason" class="form-control" placeholder="<?= Yii::t('ShopModule.base', 'Rejection reason...') ?>">
            <span class="input-group-btn">
                <?= Html::submitButton('<i class="fa fa-times"></i> ' . Yii::t('ShopModule.base', 'Reject'), [
                    'class' => 'btn btn-danger',
                    'data-confirm' => 'Reject this application?',
                ]) ?>
            </span>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php elseif ($vendor->isApproved()): ?>
<hr>
<a href="<?= Url::to(['/shop/vendor/suspend', 'id' => $vendor->id]) ?>"
   class="btn btn-warning" data-method="post" data-confirm="Suspend this vendor?">
    <i class="fa fa-ban"></i> <?= Yii::t('ShopModule.base', 'Suspend Vendor') ?>
</a>
<?php endif; ?>

<hr>
<a href="<?= Url::to(['/shop/vendor/applications']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
