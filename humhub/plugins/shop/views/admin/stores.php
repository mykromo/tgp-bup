<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
?>
<div class="panel-body">
<form method="get" action="<?= Url::to(['/shop/admin/stores']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
    <span class="text-muted" style="margin-left:10px"><?= count($vendors) ?> store(s)</span>
</form>
<?php if (empty($vendors)): ?>
    <p class="text-muted text-center">No stores found.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr>
    <th>Store</th><th>Owner</th><th>Products</th><th>Status</th><th>Since</th><th class="text-right">Actions</th>
</tr></thead>
<tbody>
<?php foreach ($vendors as $v):
    $logoUrl = $v->logo_path ? Yii::getAlias('@web') . '/' . $v->logo_path : '';
    $storeUrl = Url::to(['/shop/store/vendor-store', 'id' => $v->id]);
?>
<tr class="<?= $v->status === Vendor::STATUS_SUSPENDED ? 'danger' : '' ?>">
    <td>
        <a href="<?= $storeUrl ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit">
            <?php if ($logoUrl): ?>
                <img src="<?= Html::encode($logoUrl) ?>" style="width:36px;height:36px;border-radius:3px;object-fit:cover;border:1px solid #ddd" alt="">
            <?php else: ?>
                <div style="width:36px;height:36px;border-radius:3px;background:#e8e8e8;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa fa-shopping-bag" style="color:#bbb"></i></div>
            <?php endif; ?>
            <div>
                <strong><?= Html::encode($v->shop_name) ?></strong>
                <?php if ($v->location): ?><br><small class="text-muted"><i class="fa fa-map-marker"></i> <?= Html::encode($v->location) ?></small><?php endif; ?>
            </div>
        </a>
    </td>
    <td><?php if ($v->user): ?><a href="<?= $v->user->getUrl() ?>"><?= Html::encode($v->user->displayName) ?></a><?php else: ?>—<?php endif; ?></td>
    <td><?= (int) $v->getProducts()->count() ?></td>
    <td>
        <span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><?= Vendor::getStatusLabels()[$v->status] ?? $v->status ?></span>
        <?php if ($v->disabled_reason): ?><br><small class="text-danger"><?= Html::encode($v->disabled_reason) ?></small><?php endif; ?>
        <?php if ($v->reenable_request): ?><br><span class="label label-warning" title="<?= Html::encode($v->reenable_request) ?>">Re-enable Requested</span><?php endif; ?>
    </td>
    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDate($v->created_at) ?></td>
    <td class="text-right" style="white-space:nowrap">
        <a href="<?= $storeUrl ?>" class="btn btn-default btn-sm" title="View Store"><i class="fa fa-eye"></i></a>
        <?php if ($v->status === Vendor::STATUS_APPROVED): ?>
            <?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $v->id]), 'post', ['style' => 'display:inline']) ?>
            <div class="input-group" style="display:inline-table;width:auto">
                <input type="text" name="reason" class="form-control input-sm" placeholder="Reason..." style="width:120px">
                <span class="input-group-btn"><?= Html::submitButton('<i class="fa fa-ban"></i>', ['class' => 'btn btn-danger btn-sm', 'data-confirm' => 'Disable this store?', 'title' => 'Disable']) ?></span>
            </div>
            <?= Html::endForm() ?>
        <?php elseif ($v->status === Vendor::STATUS_SUSPENDED): ?>
            <a href="<?= Url::to(['/shop/admin/enable-store', 'id' => $v->id]) ?>" class="btn btn-success btn-sm" data-method="post" data-confirm="Re-enable this store?" title="Enable"><i class="fa fa-check"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
</div>
