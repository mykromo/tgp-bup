<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'My Delivery Addresses');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-map-marker"></i> <?= $this->title ?></strong>
    <a href="<?= Url::to(['/shop/address/create']) ?>" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Add Address</a>
</div>
<div class="panel-body">
<?php if (empty($addresses)): ?>
    <p class="text-muted text-center">No delivery addresses saved yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Label</th><th>Recipient</th><th>Address</th><th>Phone</th><th></th></tr></thead>
<tbody>
<?php foreach ($addresses as $a): ?>
<tr>
    <td><?= Html::encode($a->label) ?> <?= $a->is_default ? '<span class="label label-info">Default</span>' : '' ?></td>
    <td><?= Html::encode($a->recipient_name) ?></td>
    <td><?= Html::encode($a->getFullAddress()) ?></td>
    <td><?= Html::encode($a->phone) ?></td>
    <td style="white-space:nowrap">
        <a href="<?= Url::to(['/shop/address/edit', 'id' => $a->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
        <a href="<?= Url::to(['/shop/address/delete', 'id' => $a->id]) ?>" class="btn btn-danger btn-xs" data-method="post" data-confirm="Delete this address?"><i class="fa fa-trash"></i></a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
