<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = 'Discount Codes';
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-tag"></i> <?= $this->title ?></strong>
    <a href="<?= Url::to(['/shop/seller/create-discount']) ?>" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Create Discount</a>
</div>
<div class="panel-body">
<?php if (empty($discounts)): ?>
    <p class="text-muted text-center">No discount codes yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Uses</th><th>Valid</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($discounts as $d): ?>
<tr>
    <td><code><?= Html::encode($d->code) ?></code></td>
    <td><?= ucfirst($d->type) ?></td>
    <td><?= $d->type === 'percentage' ? $d->value . '%' : '₱' . number_format($d->value, 2) ?></td>
    <td><?= $d->min_order ? '₱' . number_format($d->min_order, 2) : '—' ?></td>
    <td><?= $d->used_count ?>/<?= $d->max_uses ?: '∞' ?></td>
    <td><?= $d->starts_at ? Yii::$app->formatter->asDate($d->starts_at) . ' — ' . Yii::$app->formatter->asDate($d->expires_at) : 'Always' ?></td>
    <td><span class="label label-<?= $d->is_active ? 'success' : 'default' ?>"><?= $d->is_active ? 'Active' : 'Inactive' ?></span></td>
    <td class="text-right" style="white-space:nowrap">
        <a href="<?= Url::to(['/shop/seller/edit-discount', 'id' => $d->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
        <a href="<?= Url::to(['/shop/seller/toggle-discount', 'id' => $d->id]) ?>" class="btn btn-<?= $d->is_active ? 'warning' : 'success' ?> btn-xs" data-method="post" title="<?= $d->is_active ? 'Disable' : 'Enable' ?>"><i class="fa fa-<?= $d->is_active ? 'pause' : 'play' ?>"></i></a>
        <a href="<?= Url::to(['/shop/seller/delete-discount', 'id' => $d->id]) ?>" class="btn btn-danger btn-xs" data-method="post" data-confirm="Delete this discount?"><i class="fa fa-trash"></i></a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
