<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
$this->title = 'All Stores';
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-store"></i> <?= $this->title ?></strong>
</div>
<div class="panel-body">
<form method="get" action="<?= Url::to(['/shop/admin/stores']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</form>
<?php if (empty($vendors)): ?>
    <p class="text-muted text-center">No stores found.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr>
    <th>Owner</th><th>Shop Name</th><th>Location</th><th>Products</th><th>Status</th><th>Since</th><th class="text-right">Actions</th>
</tr></thead>
<tbody>
<?php foreach ($vendors as $v): ?>
<tr class="<?= $v->status === Vendor::STATUS_SUSPENDED ? 'danger' : '' ?>">
    <td><?php if ($v->user): ?><a href="<?= $v->user->getUrl() ?>"><?= Html::encode($v->user->displayName) ?></a><?php else: ?>—<?php endif; ?></td>
    <td><?= Html::encode($v->shop_name) ?></td>
    <td><?= Html::encode($v->location ?? '—') ?></td>
    <td><?= count($v->products) ?></td>
    <td>
        <span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><?= Vendor::getStatusLabels()[$v->status] ?? $v->status ?></span>
        <?php if ($v->disabled_reason): ?><br><small class="text-danger"><?= Html::encode($v->disabled_reason) ?></small><?php endif; ?>
        <?php if ($v->reenable_request): ?><br><span class="label label-warning">Re-enable Requested</span><?php endif; ?>
    </td>
    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDate($v->created_at) ?></td>
    <td class="text-right" style="white-space:nowrap">
        <?php if ($v->status === Vendor::STATUS_APPROVED): ?>
            <?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $v->id]), 'post', ['style' => 'display:inline']) ?>
            <div class="input-group" style="display:inline-table;width:auto">
                <input type="text" name="reason" class="form-control input-sm" placeholder="Reason..." style="width:150px">
                <span class="input-group-btn"><?= Html::submitButton('<i class="fa fa-ban"></i> Disable', ['class' => 'btn btn-danger btn-sm', 'data-confirm' => 'Disable this store?']) ?></span>
            </div>
            <?= Html::endForm() ?>
        <?php elseif ($v->status === Vendor::STATUS_SUSPENDED): ?>
            <?php if ($v->reenable_request): ?>
                <div style="display:inline-block;max-width:200px;font-size:11px;vertical-align:middle;margin-right:5px" class="text-info" title="<?= Html::encode($v->reenable_request) ?>">
                    <i class="fa fa-comment"></i> "<?= Html::encode(mb_substr($v->reenable_request, 0, 50)) ?><?= mb_strlen($v->reenable_request) > 50 ? '...' : '' ?>"
                </div>
            <?php endif; ?>
            <a href="<?= Url::to(['/shop/admin/enable-store', 'id' => $v->id]) ?>" class="btn btn-success btn-sm" data-method="post" data-confirm="Re-enable this store?"><i class="fa fa-check"></i> Enable</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
