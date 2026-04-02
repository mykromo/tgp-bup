<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = 'Change Requests';
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-exchange"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php if (empty($requests)): ?>
    <p class="text-muted text-center">No pending requests.</p>
<?php else: ?>
<?php foreach ($requests as $req): $o = $req->order; ?>
<div class="panel panel-<?= $req->type === 'cancel' ? 'danger' : 'info' ?>">
    <div class="panel-heading">
        <strong><?= ucfirst($req->type) ?> Request</strong> — Order <?= Html::encode($o->order_number ?? '') ?>
        <span class="pull-right text-muted"><?= Yii::$app->formatter->asDatetime($req->created_at) ?></span>
    </div>
    <div class="panel-body">
        <p><strong>Buyer:</strong> <?= $req->user ? Html::encode($req->user->displayName) : '—' ?></p>
        <?php if ($req->details): ?><p><strong>Reason:</strong> <?= Html::encode($req->details) ?></p><?php endif; ?>
        <?php if ($req->new_quantity): ?><p><strong>New Quantity:</strong> <?= $req->new_quantity ?></p><?php endif; ?>
        <?php if ($req->new_address_id && $req->newAddress): ?><p><strong>New Address:</strong> <?= Html::encode($req->newAddress->getFullAddress()) ?></p><?php endif; ?>
        <hr>
        <a href="<?= Url::to(['/shop/seller/approve-request', 'id' => $req->id]) ?>" class="btn btn-success btn-sm" data-method="post" data-confirm="Approve this request?"><i class="fa fa-check"></i> Approve</a>
        <?= Html::beginForm(Url::to(['/shop/seller/reject-request', 'id' => $req->id]), 'post', ['style' => 'display:inline']) ?>
        <div class="input-group" style="display:inline-table;width:auto">
            <input type="text" name="response" class="form-control input-sm" placeholder="Reason..." style="width:200px">
            <span class="input-group-btn"><?= Html::submitButton('<i class="fa fa-times"></i> Reject', ['class' => 'btn btn-danger btn-sm']) ?></span>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
