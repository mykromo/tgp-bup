<?php
use yii\helpers\Url;
?>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-6 col-md-3" style="margin-bottom:15px">
            <a href="<?= Url::to(['/shop/admin/applications']) ?>" class="well text-center" style="display:block;margin:0;text-decoration:none;color:inherit">
                <h2 style="margin:0;color:<?= $pendingApps > 0 ? '#337ab7' : '#555' ?>"><?= $pendingApps ?></h2>
                <small class="text-muted">Pending Applications</small>
            </a>
        </div>
        <div class="col-sm-6 col-md-3" style="margin-bottom:15px">
            <a href="<?= Url::to(['/shop/admin/stores']) ?>" class="well text-center" style="display:block;margin:0;text-decoration:none;color:inherit">
                <h2 style="margin:0;color:#5cb85c"><?= $activeStores ?></h2>
                <small class="text-muted">Active Stores</small>
            </a>
        </div>
        <div class="col-sm-6 col-md-3" style="margin-bottom:15px">
            <a href="<?= Url::to(['/shop/admin/stores', 'status' => 'suspended']) ?>" class="well text-center" style="display:block;margin:0;text-decoration:none;color:inherit">
                <h2 style="margin:0;color:#c9302c"><?= $suspendedStores ?></h2>
                <small class="text-muted">Suspended Stores</small>
            </a>
        </div>
        <div class="col-sm-6 col-md-3" style="margin-bottom:15px">
            <a href="<?= Url::to(['/shop/admin/orders', 'status' => 'paid']) ?>" class="well text-center" style="display:block;margin:0;text-decoration:none;color:inherit">
                <h2 style="margin:0;color:<?= $pendingOrders > 0 ? '#f0ad4e' : '#555' ?>"><?= $pendingOrders ?></h2>
                <small class="text-muted">Unverified Orders</small>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h5>Overview</h5>
            <table class="table table-condensed">
                <tr><td><i class="fa fa-store fa-fw text-muted"></i> Total Stores</td><td class="text-right"><strong><?= $totalStores ?></strong></td></tr>
                <tr><td><i class="fa fa-cube fa-fw text-muted"></i> Total Products</td><td class="text-right"><strong><?= $totalProducts ?></strong></td></tr>
                <tr><td><i class="fa fa-list fa-fw text-muted"></i> Total Orders</td><td class="text-right"><strong><?= $totalOrders ?></strong></td></tr>
            </table>
        </div>
        <div class="col-sm-6">
            <h5>Quick Actions</h5>
            <a href="<?= Url::to(['/shop/admin/applications']) ?>" class="btn btn-default btn-sm btn-block text-left"><i class="fa fa-file-text fa-fw"></i> Review Applications <?php if ($pendingApps > 0): ?><span class="badge"><?= $pendingApps ?></span><?php endif; ?></a>
            <a href="<?= Url::to(['/shop/admin/stores']) ?>" class="btn btn-default btn-sm btn-block text-left"><i class="fa fa-store fa-fw"></i> Manage Stores</a>
            <a href="<?= Url::to(['/shop/admin/orders']) ?>" class="btn btn-default btn-sm btn-block text-left"><i class="fa fa-list fa-fw"></i> View All Orders <?php if ($pendingOrders > 0): ?><span class="badge"><?= $pendingOrders ?></span><?php endif; ?></a>
            <a href="<?= Url::to(['/shop/admin/settings']) ?>" class="btn btn-default btn-sm btn-block text-left"><i class="fa fa-cog fa-fw"></i> Shop Settings</a>
        </div>
    </div>
</div>
