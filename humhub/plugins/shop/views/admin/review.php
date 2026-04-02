<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
?>
<div class="panel-body">
<div class="row">
    <div class="col-sm-6">
        <h4>Applicant</h4>
        <div class="well">
            <?php if ($vendor->user): ?>
            <div class="media">
                <div class="media-left">
                    <img src="<?= $vendor->user->getProfileImage()->getUrl() ?>" class="img-circle" width="48" height="48">
                </div>
                <div class="media-body">
                    <h5 style="margin-top:0"><a href="<?= $vendor->user->getUrl() ?>"><?= Html::encode($vendor->user->displayName) ?></a></h5>
                    <p class="text-muted" style="margin:0"><?= Html::encode($vendor->user->email) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <h4>Shop Details</h4>
        <table class="table table-condensed">
            <tr><td style="width:120px">Shop Name</td><td><strong><?= Html::encode($vendor->shop_name) ?></strong></td></tr>
            <tr><td>Description</td><td><?= Html::encode($vendor->description ?: '—') ?></td></tr>
            <tr><td>Location</td><td><?= Html::encode($vendor->location ?: '—') ?></td></tr>
            <tr><td>Applied</td><td><?= Yii::$app->formatter->asDatetime($vendor->created_at) ?></td></tr>
            <tr><td>Status</td><td><span class="label label-<?= Vendor::getStatusBadge($vendor->status) ?>"><?= Vendor::getStatusLabels()[$vendor->status] ?></span></td></tr>
            <?php if ($vendor->reviewed_at): ?>
            <tr><td>Reviewed</td><td><?= Yii::$app->formatter->asDatetime($vendor->reviewed_at) ?> by <?= $vendor->reviewer ? Html::encode($vendor->reviewer->displayName) : '—' ?></td></tr>
            <?php endif; ?>
            <?php if ($vendor->rejection_reason): ?>
            <tr><td>Rejection Reason</td><td class="text-danger"><?= Html::encode($vendor->rejection_reason) ?></td></tr>
            <?php endif; ?>
        </table>
        <?php if ($vendor->isApproved()): ?>
            <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id]) ?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i> View Store Profile</a>
        <?php endif; ?>
    </div>
    <div class="col-sm-6">
        <h4>Submitted Documents</h4>
        <?php $docLabels = Vendor::getRequiredDocuments(); ?>
        <?php if (!empty($vendor->documents)): ?>
            <?php foreach ($vendor->documents as $doc): ?>
            <div class="well well-sm" style="margin-bottom:8px">
                <strong><?= Html::encode($docLabels[$doc->document_type] ?? $doc->document_type) ?></strong><br>
                <a href="<?= Yii::getAlias('@web') . '/' . $doc->file_path ?>" target="_blank" data-pjax-prevent>
                    <i class="fa fa-file"></i> <?= Html::encode($doc->file_name) ?>
                </a>
                <small class="text-muted">(<?= Yii::$app->formatter->asShortSize($doc->file_size) ?>)</small>
                <?php if ($doc->notes): ?><br><small class="text-info"><?= Html::encode($doc->notes) ?></small><?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-danger"><i class="fa fa-warning"></i> No documents uploaded.</div>
        <?php endif; ?>
    </div>
</div>

<?php if ($vendor->isPending()): ?>
<hr>
<div class="row">
    <div class="col-sm-6">
        <a href="<?= Url::to(['/shop/admin/approve', 'id' => $vendor->id]) ?>"
           class="btn btn-success btn-block" data-method="post" data-confirm="Approve this vendor application?">
            <i class="fa fa-check"></i> Approve Application
        </a>
    </div>
    <div class="col-sm-6">
        <?= Html::beginForm(Url::to(['/shop/admin/reject', 'id' => $vendor->id]), 'post') ?>
        <div class="input-group">
            <input type="text" name="reason" class="form-control" placeholder="Rejection reason...">
            <span class="input-group-btn">
                <?= Html::submitButton('<i class="fa fa-times"></i> Reject', [
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
<?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $vendor->id]), 'post', ['class' => 'form-inline']) ?>
<div class="input-group" style="width:auto">
    <input type="text" name="reason" class="form-control input-sm" placeholder="Suspension reason..." style="width:250px">
    <span class="input-group-btn">
        <?= Html::submitButton('<i class="fa fa-ban"></i> Suspend Store', ['class' => 'btn btn-warning btn-sm', 'data-confirm' => 'Suspend this store?']) ?>
    </span>
</div>
<?= Html::endForm() ?>
<?php endif; ?>

<hr>
<a href="<?= Url::to(['/shop/admin/applications']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Applications</a>
</div>
