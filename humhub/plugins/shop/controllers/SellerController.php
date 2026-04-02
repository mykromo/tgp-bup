<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\Category;
use humhub\modules\shop\models\Discount;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\models\ProductImage;
use humhub\modules\shop\models\ProductVariant;
use humhub\modules\shop\models\Vendor;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class SellerController extends Controller
{
    public $subLayout = '@shop/views/layouts/main';

    private function getVendor(): Vendor
    {
        if (Yii::$app->user->isGuest) throw new ForbiddenHttpException();
        $vendor = Vendor::getForUser(Yii::$app->user->id);
        if (!$vendor || !$vendor->isApproved()) throw new ForbiddenHttpException('You must be an approved vendor.');
        return $vendor;
    }

    public function actionDashboard()
    {
        $vendor = $this->getVendor();
        $products = Product::find()->where(['vendor_id' => $vendor->id])->orderBy(['sort_order' => SORT_ASC])->all();
        return $this->render('dashboard', ['vendor' => $vendor, 'products' => $products]);
    }

    public function actionCreateProduct()
    {
        $vendor = $this->getVendor();
        $model = new Product();
        $model->vendor_id = $vendor->id;
        $model->currency = 'PHP';
        $model->sort_order = 0;
        $model->is_active = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->handleImageUploads($model);
            \humhub\modules\shop\helpers\ShopCache::invalidateProduct($model->id);
            $this->view->saved();
            return $this->redirect(['/shop/seller/dashboard']);
        }

        return $this->render('product-form', ['model' => $model, 'categories' => Category::getDropdownList()]);
    }

    public function actionEditProduct($id)
    {
        $vendor = $this->getVendor();
        $model = Product::findOne(['id' => $id, 'vendor_id' => $vendor->id]);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->handleImageUploads($model);
            \humhub\modules\shop\helpers\ShopCache::invalidateProduct($model->id);
            $this->view->saved();
            return $this->redirect(['/shop/seller/dashboard']);
        }

        return $this->render('product-form', ['model' => $model, 'categories' => Category::getDropdownList()]);
    }

    // ── Variants ──

    public function actionVariants($productId)
    {
        $vendor = $this->getVendor();
        $product = Product::findOne(['id' => $productId, 'vendor_id' => $vendor->id]);
        if (!$product) throw new NotFoundHttpException();

        $variants = ProductVariant::find()->where(['product_id' => $productId])->all();
        return $this->render('variants', ['product' => $product, 'variants' => $variants]);
    }

    public function actionAddVariant($productId)
    {
        $vendor = $this->getVendor();
        $product = Product::findOne(['id' => $productId, 'vendor_id' => $vendor->id]);
        if (!$product) throw new NotFoundHttpException();

        $model = new ProductVariant();
        $model->product_id = $productId;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect(['/shop/seller/variants', 'productId' => $productId]);
        }

        return $this->render('variant-form', ['model' => $model, 'product' => $product]);
    }

    public function actionDeleteVariant($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $variant = ProductVariant::findOne($id);
        if ($variant && $variant->product && $variant->product->vendor_id === $vendor->id) {
            $productId = $variant->product_id;
            $variant->delete();
            return $this->redirect(['/shop/seller/variants', 'productId' => $productId]);
        }
        return $this->redirect(['/shop/seller/dashboard']);
    }

    // ── Discounts ──

    public function actionDiscounts()
    {
        $vendor = $this->getVendor();
        $discounts = Discount::find()->where(['vendor_id' => $vendor->id])->all();
        return $this->render('discounts', ['discounts' => $discounts]);
    }

    public function actionCreateDiscount()
    {
        $vendor = $this->getVendor();
        $model = new Discount();
        $model->vendor_id = $vendor->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect(['/shop/seller/discounts']);
        }

        return $this->render('discount-form', ['model' => $model]);
    }

    // ── Image upload helper ──

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

    public function actionDeleteImage($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $img = ProductImage::findOne($id);
        if ($img && $img->product && $img->product->vendor_id === $vendor->id) {
            $fullPath = Yii::getAlias('@webroot') . '/' . $img->file_path;
            if (file_exists($fullPath)) @unlink($fullPath);
            $productId = $img->product_id;
            $img->delete();
            return $this->redirect(['/shop/seller/edit-product', 'id' => $productId]);
        }
        return $this->redirect(['/shop/seller/dashboard']);
    }
}

    // ── Seller Order Management ──

    public function actionOrders()
    {
        $vendor = $this->getVendor();
        $status = Yii::$app->request->get('status');

        $query = Order::find()
            ->innerJoin('shop_order_item', 'shop_order_item.order_id = shop_order.id')
            ->innerJoin('shop_product', 'shop_product.id = shop_order_item.product_id')
            ->where(['shop_product.vendor_id' => $vendor->id])
            ->groupBy('shop_order.id')
            ->orderBy(['shop_order.created_at' => SORT_DESC]);

        if ($status) $query->andWhere(['shop_order.status' => $status]);

        return $this->render('orders', [
            'orders' => $query->all(),
            'selectedStatus' => $status,
        ]);
    }

    public function actionViewOrder($id)
    {
        $vendor = $this->getVendor();
        $order = $this->findSellerOrder($id, $vendor);
        $messengerUrl = \humhub\modules\shop\helpers\ShopNotify::getMessengerUrl($order, $order->user_id);

        return $this->render('order-detail', ['order' => $order, 'messengerUrl' => $messengerUrl]);
    }

    public function actionVerifyOrder($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $order = $this->findSellerOrder($id, $vendor);

        $order->status = Order::STATUS_VERIFIED;
        $order->payment_verified = 1;
        $order->verified_by = Yii::$app->user->id;
        $order->verified_at = date('Y-m-d H:i:s');
        $order->save(false);

        \humhub\modules\shop\helpers\ShopNotify::notifyBuyer($order,
            'Order ' . $order->order_number . ' verified',
            '<p>Your order <strong>' . $order->order_number . '</strong> has been verified by the seller. It will be processed shortly.</p>'
        );

        $this->view->saved();
        return $this->redirect(['/shop/seller/view-order', 'id' => $id]);
    }

    public function actionRejectOrder($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $order = $this->findSellerOrder($id, $vendor);

        $reason = Yii::$app->request->post('reason', '');
        $order->status = Order::STATUS_REJECTED;
        $order->rejection_reason = $reason;
        $order->rejected_by = Yii::$app->user->id;
        $order->rejected_at = date('Y-m-d H:i:s');
        $order->save(false);

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock !== null) {
                $item->product->updateCounters(['stock' => $item->quantity]);
            }
        }

        $reasonText = $reason ? '<p>Reason: ' . \humhub\libs\Html::encode($reason) . '</p>' : '';
        \humhub\modules\shop\helpers\ShopNotify::notifyBuyer($order,
            'Order ' . $order->order_number . ' rejected',
            '<p>Your order <strong>' . $order->order_number . '</strong> has been rejected by the seller.</p>' . $reasonText
        );

        $this->view->saved();
        return $this->redirect(['/shop/seller/orders']);
    }

    private function findSellerOrder(int $id, Vendor $vendor): Order
    {
        $order = Order::find()
            ->innerJoin('shop_order_item', 'shop_order_item.order_id = shop_order.id')
            ->innerJoin('shop_product', 'shop_product.id = shop_order_item.product_id')
            ->where(['shop_order.id' => $id, 'shop_product.vendor_id' => $vendor->id])
            ->one();
        if (!$order) throw new NotFoundHttpException();
        return $order;
    }

    // ── Handle buyer change requests ──

    public function actionRequests()
    {
        $vendor = $this->getVendor();
        $requests = \humhub\modules\shop\models\OrderRequest::find()
            ->innerJoin('shop_order', 'shop_order.id = shop_order_request.order_id')
            ->innerJoin('shop_order_item', 'shop_order_item.order_id = shop_order.id')
            ->innerJoin('shop_product', 'shop_product.id = shop_order_item.product_id')
            ->where(['shop_product.vendor_id' => $vendor->id, 'shop_order_request.status' => 'pending'])
            ->orderBy(['shop_order_request.created_at' => SORT_DESC])
            ->all();

        return $this->render('requests', ['requests' => $requests]);
    }

    public function actionApproveRequest($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $req = \humhub\modules\shop\models\OrderRequest::findOne($id);
        if (!$req) throw new NotFoundHttpException();

        $req->status = \humhub\modules\shop\models\OrderRequest::STATUS_APPROVED;
        $req->seller_response = Yii::$app->request->post('response', 'Approved');
        $req->responded_at = date('Y-m-d H:i:s');
        $req->save(false);

        $order = $req->order;

        // Apply changes
        if ($req->type === 'cancel') {
            $order->status = Order::STATUS_CANCELLED;
            $order->save(false);
            foreach ($order->items as $item) {
                if ($item->product && $item->product->stock !== null) {
                    $item->product->updateCounters(['stock' => $item->quantity]);
                }
            }
        } else {
            if ($req->new_address_id) {
                $addr = \humhub\modules\shop\models\DeliveryAddress::findOne($req->new_address_id);
                if ($addr) {
                    $order->address_id = $addr->id;
                    $order->delivery_address = $addr->recipient_name . "\n" . $addr->phone . "\n" . $addr->getFullAddress();
                }
            }
            if ($req->new_quantity && $order->items) {
                $item = $order->items[0];
                $oldQty = $item->quantity;
                $item->quantity = $req->new_quantity;
                $item->total_price = $item->unit_price * $req->new_quantity;
                $item->save(false);
                $order->total_amount = $item->total_price;
                if ($item->product && $item->product->stock !== null) {
                    $item->product->updateCounters(['stock' => $oldQty - $req->new_quantity]);
                }
            }
            $order->save(false);
        }

        \humhub\modules\shop\helpers\ShopNotify::notifyBuyer($order,
            'Your request for order ' . $order->order_number . ' has been approved',
            '<p>Your ' . $req->type . ' request for order <strong>' . $order->order_number . '</strong> has been approved.</p>'
        );

        $this->view->saved();
        return $this->redirect(['/shop/seller/requests']);
    }

    public function actionRejectRequest($id)
    {
        $vendor = $this->getVendor();
        $this->forcePostRequest();
        $req = \humhub\modules\shop\models\OrderRequest::findOne($id);
        if (!$req) throw new NotFoundHttpException();

        $req->status = \humhub\modules\shop\models\OrderRequest::STATUS_REJECTED;
        $req->seller_response = Yii::$app->request->post('response', 'Rejected');
        $req->responded_at = date('Y-m-d H:i:s');
        $req->save(false);

        \humhub\modules\shop\helpers\ShopNotify::notifyBuyer($req->order,
            'Your request for order ' . $req->order->order_number . ' has been rejected',
            '<p>Your ' . $req->type . ' request for order <strong>' . $req->order->order_number . '</strong> has been rejected.</p><p>Reason: ' . \humhub\libs\Html::encode($req->seller_response) . '</p>'
        );

        $this->view->saved();
        return $this->redirect(['/shop/seller/requests']);
    }
