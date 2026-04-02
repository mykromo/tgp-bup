<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = 'Edit Order ' . $order->order_number;
$item = $order->items[0] ?? null;
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-pencil"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?= Html::beginForm('', 'post') ?>
<div class="form-group">
    <label>Delivery Address</label>
    <select name="address_id" class="form-control">
        <?php foreach ($addresses as $a): ?>
            <option value="<?= $a->id ?>" <?= $order->address_id == $a->id ? 'selected' : '' ?>><?= Html::encode($a->getDropdownLabel()) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($item): ?>
<div class="form-group">
    <label>Quantity (current: <?= $item->quantity ?>)</label>
    <input type="number" name="quantity" value="<?= $item->quantity ?>" min="1" class="form-control" style="width:120px">
</div>
<?php endif; ?>
<hr>
<?= Html::submitButton('<i class="fa fa-check"></i> Save Changes', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-default">Cancel</a>
<?= Html::endForm() ?>
</div></div>
