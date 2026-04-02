<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
?>
<div class="panel-body">
<div style="margin-bottom:15px">
    <a href="<?= Url::to(['/shop/admin/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form method="get" action="<?= Url::to(['/shop/admin/applications']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
    <span class="text-muted" style="margin-left:10px"><?= count($vendors) ?> application(s)</span>
</form>
<?php if (empty($vendors)): ?>
    <p class="text-muted text-center">No applications found.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Applicant</th><th>Shop Name</th><th>Documents</th><th>Status</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($vendors as $v): ?>
<tr>
    <td>
        <?php if ($v->user): ?>
        <div style="display:flex;align-items:center;gap:8px">
            <img src="<?= $v->user->getProfileImage()->getUrl() ?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover" alt="">
            <a href="<?= $v->user->getUrl() ?>"><?= Html::encode($v->user->displayName) ?></a>
        </div>
        <?php else: ?>—<?php endif; ?>
    </td>
    <td><?= Html::encode($v->shop_name) ?></td>
    <td><span class="badge"><?= count($v->documents) ?></span> files</td>
    <td><span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><?= Vendor::getStatusLabels()[$v->status] ?></span></td>
    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDatetime($v->created_at) ?></td>
    <td class="text-right"><a href="<?= Url::to(['/shop/admin/review', 'id' => $v->id]) ?>" class="btn btn-default btn-sm"><i class="fa fa-search"></i> Review</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
</div>
