<?php

namespace humhub\modules\shop\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\PaymentSetting;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\permissions\ManageShop;
use humhub\modules\space\models\Space;
use Yii;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AdminController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    private function requireAdmin()
    {
        if (!$this->contentContainer->permissionManager->can(ManageShop::class)) {
            throw new ForbiddenHttpException();
        }
    }

    // ── Products ──

    public function actionProducts()
    {
        $this->requireAdmin();
        $products = Product::find()->where(['space_id' => $this->contentContainer->id])->orderBy(['sort_order' => SORT_ASC])->all();
        return $this->render('products', ['products' => $products, 'contentContainer' => $this->contentContainer]);
    }

    public function actionCreateProduct()
    {
        $this->requireAdmin();
        $model = new Product();
        $model->space_id = $this->contentContainer->id;
        $model->currency = 'PHP';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/shop/admin/products'));
        }
        return $this->render('product-form', ['model' => $model, 'contentContainer' => $this->contentContainer]);
    }

    public function actionEditProduct($id)
    {
        $this->requireAdmin();
        $model = Product::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/shop/admin/products'));
        }
        return $this->render('product-form', ['model' => $model, 'contentContainer' => $this->contentContainer]);
    }

    // ── Orders ──

    public function actionOrders()
    {
        $this->requireAdmin();
        $status = Yii::$app->request->get('status');
        $query = Order::find()->where(['shop_order.space_id' => $this->contentContainer->id])->orderBy(['created_at' => SORT_DESC]);
        if ($status) $query->andWhere(['status' => $status]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('orders', [
            'orders' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'selectedStatus' => $status,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionViewOrder($id)
    {
        $this->requireAdmin();
        $order = Order::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$order) throw new NotFoundHttpException();

        return $this->render('order-detail', ['order' => $order, 'contentContainer' => $this->contentContainer]);
    }

    public function actionVerifyOrder($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $order = Order::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$order) throw new NotFoundHttpException();

        $order->status = Order::STATUS_VERIFIED;
        $order->payment_verified = 1;
        $order->verified_by = Yii::$app->user->id;
        $order->verified_at = date('Y-m-d H:i:s');
        $order->save(false);

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/shop/admin/view-order', ['id' => $id]));
    }

    public function actionCancelOrder($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $order = Order::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$order) throw new NotFoundHttpException();

        $order->status = Order::STATUS_CANCELLED;
        $order->save(false);

        // Restore stock
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->stock !== null) {
                $product->updateCounters(['stock' => $item->quantity]);
            }
        }

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/shop/admin/orders'));
    }

    // ── Payment Settings ──

    public function actionSettings()
    {
        $this->requireAdmin();
        $model = PaymentSetting::getForSpace($this->contentContainer->id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/shop/admin/settings'));
        }

        return $this->render('settings', ['model' => $model, 'contentContainer' => $this->contentContainer]);
    }
}
