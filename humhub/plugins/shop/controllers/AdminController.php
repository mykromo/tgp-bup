<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\PaymentSetting;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\models\ProductImage;
use Yii;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminController extends Controller
{
    public function init()
    {
        parent::init();
        if ($this->module) {
            $this->subLayout = $this->module->getBasePath() . '/views/layouts/main';
        }
    }

    private function requireAdmin()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->isAdmin()) {
            throw new ForbiddenHttpException();
        }
    }

    public function actionProducts()
    {
        $this->requireAdmin();
        $products = Product::find()->where(['or', ['space_id' => null], ['space_id' => 0]])->orderBy(['sort_order' => SORT_ASC])->all();
        return $this->render('products', ['products' => $products]);
    }

    public function actionCreateProduct()
    {
        $this->requireAdmin();
        $model = new Product();
        $model->space_id = null;
        $model->currency = 'PHP';
        $model->sort_order = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->handleImageUploads($model);
            $this->view->saved();
            return $this->redirect(['/shop/admin/products']);
        }
        return $this->render('product-form', ['model' => $model]);
    }

    public function actionEditProduct($id)
    {
        $this->requireAdmin();
        $model = Product::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->handleImageUploads($model);
            $this->view->saved();
            return $this->redirect(['/shop/admin/products']);
        }
        return $this->render('product-form', ['model' => $model]);
    }

    public function actionDeleteImage($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $img = ProductImage::findOne($id);
        if ($img) {
            $fullPath = Yii::getAlias('@webroot') . '/' . $img->file_path;
            if (file_exists($fullPath)) @unlink($fullPath);
            $productId = $img->product_id;
            $img->delete();
            return $this->redirect(['/shop/admin/edit-product', 'id' => $productId]);
        }
        return $this->redirect(['/shop/admin/products']);
    }

    private function handleImageUploads(Product $product): void
    {
        $files = UploadedFile::getInstancesByName('product_images');
        $uploadPath = ProductImage::getUploadPath();
        $maxOrder = (int) ProductImage::find()->where(['product_id' => $product->id])->max('sort_order');

        foreach ($files as $file) {
            if ($file->size > ProductImage::MAX_FILE_SIZE) continue;
            if (!in_array(strtolower($file->extension), ProductImage::ALLOWED_EXTENSIONS)) continue;

            $fileName = $product->id . '_' . time() . '_' . mt_rand(100, 999) . '.' . $file->extension;
            $tempPath = $uploadPath . '/tmp_' . $fileName;
            $finalPath = $uploadPath . '/' . $fileName;

            if ($file->saveAs($tempPath)) {
                if (ProductImage::resizeImage($tempPath, $finalPath)) {
                    if ($tempPath !== $finalPath) @unlink($tempPath);

                    $maxOrder++;
                    $img = new ProductImage();
                    $img->product_id = $product->id;
                    $img->file_name = $file->name;
                    $img->file_path = 'uploads/shop/products/' . $fileName;
                    $img->file_size = filesize($finalPath);
                    $img->sort_order = $maxOrder;
                    $img->created_at = date('Y-m-d H:i:s');
                    $img->save(false);
                } else {
                    @unlink($tempPath);
                }
            }
        }
    }

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

        $this->view->saved();
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

        $this->view->saved();
        return $this->redirect(['/shop/admin/orders']);
    }

    public function actionSettings()
    {
        $this->requireAdmin();
        $model = PaymentSetting::getGlobal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Save cache TTL
            $cacheTtl = max(0, min(86400, (int) Yii::$app->request->post('cacheTtl', 300)));
            Yii::$app->getModule('shop')->settings->set('cacheTtl', $cacheTtl);
            \humhub\modules\shop\helpers\ShopCache::flushAll();
            $this->view->saved();
            return $this->redirect(['/shop/admin/settings']);
        }

        return $this->render('settings', ['model' => $model]);
    }

    // ── Vendor/Store Management ──

    public function actionStores()
    {
        $this->requireAdmin();
        $status = Yii::$app->request->get('status');
        $query = \humhub\modules\shop\models\Vendor::find()->orderBy(['created_at' => SORT_DESC]);
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
        $vendor = \humhub\modules\shop\models\Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = \humhub\modules\shop\models\Vendor::STATUS_SUSPENDED;
        $vendor->disabled_reason = Yii::$app->request->post('reason', '');
        $vendor->disabled_at = date('Y-m-d H:i:s');
        $vendor->disabled_by = Yii::$app->user->id;
        $vendor->save(false);

        // Notify vendor
        \humhub\modules\shop\helpers\ShopNotify::sendEmail(
            $vendor->user->email ?? '',
            'Your shop has been disabled',
            '<p>Your shop <strong>' . \humhub\libs\Html::encode($vendor->shop_name) . '</strong> has been disabled by an administrator.</p>'
            . ($vendor->disabled_reason ? '<p>Reason: ' . \humhub\libs\Html::encode($vendor->disabled_reason) . '</p>' : '')
        );

        $this->view->saved();
        return $this->redirect(['/shop/admin/stores']);
    }

    public function actionEnableStore($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();
        $vendor = \humhub\modules\shop\models\Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = \humhub\modules\shop\models\Vendor::STATUS_APPROVED;
        $vendor->disabled_reason = null;
        $vendor->disabled_at = null;
        $vendor->disabled_by = null;
        $vendor->save(false);

        \humhub\modules\shop\helpers\ShopNotify::sendEmail(
            $vendor->user->email ?? '',
            'Your shop has been re-enabled',
            '<p>Your shop <strong>' . \humhub\libs\Html::encode($vendor->shop_name) . '</strong> has been re-enabled. You can now continue selling.</p>'
        );

        $this->view->saved();
        return $this->redirect(['/shop/admin/stores']);
    }
}
