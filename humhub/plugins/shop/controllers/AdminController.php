<?php

namespace humhub\modules\shop\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\PaymentSetting;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\models\ProductImage;
use humhub\modules\shop\models\Vendor;
use Yii;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminController extends Controller
{
    /**
     * Use the admin layout so this appears inside the Administration section.
     */
    public $subLayout = '@shop/views/admin/layout';

    public function init()
    {
        parent::init();
        $this->appendPageTitle(Yii::t('ShopModule.base', 'Shop'));
    }

    private function requireAdmin()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->isAdmin()) {
            throw new ForbiddenHttpException();
        }
    }

    // ── Dashboard ──

    public function actionIndex()
    {
        $this->requireAdmin();

        $totalStores = Vendor::find()->count();
        $pendingApps = Vendor::find()->where(['status' => Vendor::STATUS_PENDING])->count();
        $activeStores = Vendor::find()->where(['status' => Vendor::STATUS_APPROVED])->count();
        $suspendedStores = Vendor::find()->where(['status' => Vendor::STATUS_SUSPENDED])->count();
        $totalProducts = Product::find()->count();
        $totalOrders = Order::find()->count();
        $pendingOrders = Order::find()->where(['status' => Order::STATUS_PAID])->count();

        return $this->render('index', [
            'totalStores' => $totalStores,
            'pendingApps' => $pendingApps,
            'activeStores' => $activeStores,
            'suspendedStores' => $suspendedStores,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    // ── Applications ──

    public function actionApplications()
    {
        $this->requireAdmin();
        $status = Yii::$app->request->get('status', 'pending');
        $query = Vendor::find()->where(['status' => $status])->orderBy(['created_at' => SORT_DESC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('applications', [
            'vendors' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'selectedStatus' => $status,
        ]);
    }

    public function actionReview($id)
    {
        $this->requireAdmin();
        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();
        return $this->render('review', ['vendor' => $vendor]);
    }

    public function actionApprove($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_APPROVED;
        $vendor->reviewed_by = Yii::$app->user->id;
        $vendor->reviewed_at = date('Y-m-d H:i:s');
        $vendor->save(false);

        try {
            $notification = new \humhub\modules\shop\notifications\VendorApproved([
                'source' => $vendor,
                'originator' => Yii::$app->user->getIdentity(),
            ]);
            $notification->send($vendor->user);
        } catch (\Throwable $e) {
            Yii::error('Vendor approval notification failed: ' . $e->getMessage(), 'shop');
        }

        try {
            Yii::$app->mailer->compose()
                ->setTo($vendor->user->email)
                ->setSubject(Yii::t('ShopModule.base', 'Your shop application has been approved!'))
                ->setHtmlBody(
                    '<h3>' . Yii::t('ShopModule.base', 'Congratulations!') . '</h3>'
                    . '<p>' . Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been approved.', ['shopName' => $vendor->shop_name]) . '</p>'
                )->send();
        } catch (\Throwable $e) {
            Yii::error('Vendor approval email failed: ' . $e->getMessage(), 'shop');
        }

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Application approved.'));
        return $this->redirect(['/shop/admin/applications']);
    }

    public function actionReject($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_REJECTED;
        $vendor->rejection_reason = Yii::$app->request->post('reason', '');
        $vendor->reviewed_by = Yii::$app->user->id;
        $vendor->reviewed_at = date('Y-m-d H:i:s');
        $vendor->save(false);

        try {
            $notification = new \humhub\modules\shop\notifications\VendorRejected([
                'source' => $vendor,
                'originator' => Yii::$app->user->getIdentity(),
            ]);
            $notification->send($vendor->user);
        } catch (\Throwable $e) {
            Yii::error('Vendor rejection notification failed: ' . $e->getMessage(), 'shop');
        }

        try {
            Yii::$app->mailer->compose()
                ->setTo($vendor->user->email)
                ->setSubject(Yii::t('ShopModule.base', 'Your shop application has been rejected'))
                ->setHtmlBody(
                    '<p>' . Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been rejected.', ['shopName' => $vendor->shop_name]) . '</p>'
                    . ($vendor->rejection_reason ? '<p>Reason: ' . \humhub\libs\Html::encode($vendor->rejection_reason) . '</p>' : '')
                )->send();
        } catch (\Throwable $e) {
            Yii::error('Vendor rejection email failed: ' . $e->getMessage(), 'shop');
        }

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Application rejected.'));
        return $this->redirect(['/shop/admin/applications']);
    }

    // ── Stores ──

    public function actionStores()
    {
        $this->requireAdmin();
        $status = Yii::$app->request->get('status');
        $query = Vendor::find()->orderBy(['created_at' => SORT_DESC]);
        if ($status) $query->andWhere(['status' => $status]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('stores', [
            'vendors' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'selectedStatus' => $status,
        ]);
    }

    public function actionDisableStore($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_SUSPENDED;
        $vendor->disabled_reason = Yii::$app->request->post('reason', '');
        $vendor->disabled_at = date('Y-m-d H:i:s');
        $vendor->disabled_by = Yii::$app->user->id;
        $vendor->save(false);

        \humhub\modules\shop\helpers\ShopNotify::sendEmail(
            $vendor->user->email ?? '',
            'Your shop has been disabled',
            '<p>Your shop <strong>' . \humhub\libs\Html::encode($vendor->shop_name) . '</strong> has been disabled.</p>'
            . ($vendor->disabled_reason ? '<p>Reason: ' . \humhub\libs\Html::encode($vendor->disabled_reason) . '</p>' : '')
        );

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Store disabled.'));
        return $this->redirect(['/shop/admin/stores']);
    }

    public function actionEnableStore($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_APPROVED;
        $vendor->disabled_reason = null;
        $vendor->disabled_at = null;
        $vendor->disabled_by = null;
        $vendor->reenable_request = null;
        $vendor->reenable_requested_at = null;
        $vendor->save(false);

        \humhub\modules\shop\helpers\ShopNotify::sendEmail(
            $vendor->user->email ?? '',
            'Your shop has been re-enabled',
            '<p>Your shop <strong>' . \humhub\libs\Html::encode($vendor->shop_name) . '</strong> has been re-enabled.</p>'
        );

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Store enabled.'));
        return $this->redirect(['/shop/admin/stores']);
    }

    // ── Products ──

    public function actionProducts()
    {
        $this->requireAdmin();
        $query = Product::find()->orderBy(['sort_order' => SORT_ASC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 30]);
        return $this->render('products', [
            'products' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
        ]);
    }

    // ── Orders ──

    public function actionOrders()
    {
        $this->requireAdmin();
        $status = Yii::$app->request->get('status');
        $query = Order::find()->orderBy(['created_at' => SORT_DESC]);
        if ($status) $query->andWhere(['status' => $status]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('orders', [
            'orders' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'selectedStatus' => $status,
        ]);
    }

    public function actionViewOrder($id)
    {
        $this->requireAdmin();
        $order = Order::findOne($id);
        if (!$order) throw new NotFoundHttpException();
        return $this->render('order-detail', ['order' => $order]);
    }

    public function actionVerifyOrder($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $order = Order::findOne($id);
        if (!$order) throw new NotFoundHttpException();

        $order->status = Order::STATUS_VERIFIED;
        $order->payment_verified = 1;
        $order->verified_by = Yii::$app->user->id;
        $order->verified_at = date('Y-m-d H:i:s');
        $order->save(false);

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Order verified.'));
        return $this->redirect(['/shop/admin/view-order', 'id' => $id]);
    }

    public function actionCancelOrder($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $order = Order::findOne($id);
        if (!$order) throw new NotFoundHttpException();

        $order->status = Order::STATUS_CANCELLED;
        $order->save(false);

        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock !== null) {
                $item->product->updateCounters(['stock' => $item->quantity]);
            }
        }

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Order cancelled.'));
        return $this->redirect(['/shop/admin/orders']);
    }

    // ── Settings ──

    public function actionSettings()
    {
        $this->requireAdmin();
        $model = PaymentSetting::getGlobal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $cacheTtl = max(0, min(86400, (int) Yii::$app->request->post('cacheTtl', 300)));
            Yii::$app->getModule('shop')->settings->set('cacheTtl', $cacheTtl);
            \humhub\modules\shop\helpers\ShopCache::flushAll();
            Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Settings saved.'));
            return $this->redirect(['/shop/admin/settings']);
        }

        return $this->render('settings', ['model' => $model]);
    }
}
