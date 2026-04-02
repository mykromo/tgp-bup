<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Vendor Applications');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-users"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<form method="get" action="<?= Url::to(['/shop/vendor/applications']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= $selectedStatus === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</form>
<?php if (empty($vendors)): ?>
    <p class="text-muted text-center">No applications found.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Applicant</th><th>Shop Name</th><th>Documents</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php foreach ($vendors as $v): ?>
<tr>
    <td><?php if ($v->user): ?><a href="<?= $v->user->getUrl() ?>"><?= Html::encode($v->user->displayName) ?></a><?php endif; ?></td>
    <td><?= Html::encode($v->shop_name) ?></td>
    <td><?= count($v->documents) ?> files</td>
    <td><span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><?= Vendor::getStatusLabels()[$v->status] ?></span></td>
    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDatetime($v->created_at) ?></td>
    <td><a href="<?= Url::to(['/shop/vendor/review', 'id' => $v->id]) ?>" class="btn btn-default btn-xs">Review</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
