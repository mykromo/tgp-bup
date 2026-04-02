<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\Category;
use humhub\modules\shop\models\FavoriteStore;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\OrderItem;
use humhub\modules\shop\models\PaymentSetting;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\models\Vendor;
use humhub\modules\shop\models\Wishlist;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class StoreController extends Controller
{
    public function init()
    {
        parent::init();
        $this->subLayout = '@shop/views/layouts/shop';
    }

    public function actionIndex()
    {
        // Administrators should use the Administration panel, not the shop storefront
        if (!Yii::$app->user->isGuest && Yii::$app->user->isAdmin()) {
            return $this->redirect(['/shop/admin/index']);
        }

        $keyword = Yii::$app->request->get('q');
        $categoryId = Yii::$app->request->get('category');
        $location = Yii::$app->request->get('location');

        $query = Product::find()
            ->where(['shop_product.is_active' => 1])
            ->leftJoin('shop_vendor', 'shop_vendor.id = shop_product.vendor_id')
            ->andWhere(['or',
                ['shop_product.vendor_id' => null],
                ['shop_vendor.status' => Vendor::STATUS_APPROVED],
            ]);

        if ($keyword) {
            $query->andWhere(['or',
                ['like', 'shop_product.name', $keyword],
                ['like', 'shop_product.description', $keyword],
            ]);
        }
        if ($categoryId) {
            $query->andWhere(['shop_product.category_id' => $categoryId]);
        }
        if ($location) {
            $query->andWhere(['or',
                ['like', 'shop_product.location', $location],
                ['like', 'shop_vendor.location', $location],
            ]);
        }

        $query->orderBy(['shop_product.sort_order' => SORT_ASC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 12]);

        $isVendor = !Yii::$app->user->isGuest ? Vendor::getForUser(Yii::$app->user->id) : null;

        return $this->render('index', [
            'products' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'isVendor' => $isVendor,
            'categories' => Category::getDropdownList(),
            'keyword' => $keyword,
            'selectedCategory' => $categoryId,
            'selectedLocation' => $location,
        ]);
    }

    public function actionView($id)
    {
        $product = \humhub\modules\shop\helpers\ShopCache::getProduct($id);
        if (!$product) throw new NotFoundHttpException();
        return $this->render('view', ['product' => $product]);
    }

    public function actionBuy($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        // Administrators cannot buy — they are purely for managing stores
        if (Yii::$app->user->isAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Administrators cannot purchase items. Admin accounts are for store management only.'));
            return $this->redirect(['/shop/store/view', 'id' => $id]);
        }

        $product = Product::findOne(['id' => $id, 'is_active' => 1]);
        if (!$product || !$product->isInStock()) throw new NotFoundHttpException();

        // Block purchases from suspended vendors
        if ($product->vendor && $product->vendor->status === Vendor::STATUS_SUSPENDED) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'This store is currently suspended. Purchasing is unavailable.'));
            return $this->redirect(['/shop/store/view', 'id' => $id]);
        }

        $settings = PaymentSetting::getGlobal();
        $vendor = $product->vendor;
        $user = Yii::$app->user->getIdentity();

        if (Yii::$app->request->isPost && Yii::$app->request->post('confirm')) {
            $paymentRef = Yii::$app->request->post('payment_reference');
            $paymentMethod = Yii::$app->request->post('payment_method');
            $quantity = max(1, (int) Yii::$app->request->post('quantity', 1));

            if (empty($paymentRef)) {
                Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Payment reference number is required.'));
                return $this->redirect(['/shop/store/buy', 'id' => $id]);
            }

            $total = $product->getEffectivePrice() * $quantity;
            $order = new Order();
            $order->space_id = null;
            $order->user_id = $user->id;
            $order->total_amount = $total;
            $order->currency = $product->currency;
            $order->payment_reference = $paymentRef;
            $order->payment_method = $paymentMethod;
            $order->payment_date = date('Y-m-d H:i:s');
            $order->status = Order::STATUS_PAID;
            $order->buyer_name = $user->displayName;
            $order->buyer_email = $user->email;
            $order->created_by = $user->id;

            // Save delivery address
            $addressId = (int) Yii::$app->request->post('address_id');
            if ($addressId) {
                $addr = \humhub\modules\shop\models\DeliveryAddress::findOne(['id' => $addressId, 'user_id' => $user->id]);
                if ($addr) {
                    $order->address_id = $addr->id;
                    $order->delivery_address = $addr->recipient_name . "\n" . $addr->phone . "\n" . $addr->getFullAddress();
                }
            }

            if ($order->save()) {
                $item = new OrderItem();
                $item->order_id = $order->id;
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->quantity = $quantity;
                $item->unit_price = $product->price;
                $item->total_price = $total;
                $item->save();

                if ($product->stock !== null) {
                    $product->updateCounters(['stock' => -$quantity]);
                }

                // Notify buyer and seller
                \humhub\modules\shop\helpers\ShopNotify::notifyBoth(
                    $order,
                    'Order ' . $order->order_number . ' submitted',
                    '<p>Your order <strong>' . $order->order_number . '</strong> for <strong>' . $product->name . '</strong> has been submitted and is awaiting verification.</p>',
                    'New order received: ' . $order->order_number,
                    '<p>A new order <strong>' . $order->order_number . '</strong> for <strong>' . $product->name . '</strong> has been placed by <strong>' . $user->displayName . '</strong>.</p>'
                );

                return $this->redirect(['/shop/store/order-confirmation', 'id' => $order->id]);
            }
        }

        return $this->render('buy', [
            'product' => $product,
            'settings' => $settings,
            'vendor' => $vendor,
            'user' => $user,
        ]);
    }

    public function actionOrderConfirmation($id)
    {
        $order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$order) throw new NotFoundHttpException();
        return $this->render('order-confirmation', ['order' => $order]);
    }

    /**
     * Buyer edits order directly (only if status is 'paid' = unacknowledged).
     */
    public function actionEditOrder($id)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Administrators cannot place or manage orders.'));
            return $this->redirect(['/shop/store/index']);
        }
        $order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$order) throw new NotFoundHttpException();

        // Direct edit only allowed when unacknowledged (paid but not verified)
        if ($order->status !== Order::STATUS_PAID) {
            Yii::$app->session->setFlash('error', 'This order can no longer be edited directly. Submit a request instead.');
            return $this->redirect(['/shop/store/my-orders']);
        }

        $addresses = \humhub\modules\shop\models\DeliveryAddress::getForUser(Yii::$app->user->id);

        if (Yii::$app->request->isPost) {
            $newAddrId = (int) Yii::$app->request->post('address_id');
            $newQty = max(1, (int) Yii::$app->request->post('quantity', 1));
            $item = $order->items[0] ?? null;

            if ($newAddrId) {
                $addr = \humhub\modules\shop\models\DeliveryAddress::findOne(['id' => $newAddrId, 'user_id' => Yii::$app->user->id]);
                if ($addr) {
                    $order->address_id = $addr->id;
                    $order->delivery_address = $addr->recipient_name . "\n" . $addr->phone . "\n" . $addr->getFullAddress();
                }
            }

            if ($item && $newQty !== $item->quantity) {
                $oldQty = $item->quantity;
                $item->quantity = $newQty;
                $item->total_price = $item->unit_price * $newQty;
                $item->save(false);
                $order->total_amount = $item->total_price;
                // Adjust stock
                if ($item->product && $item->product->stock !== null) {
                    $item->product->updateCounters(['stock' => $oldQty - $newQty]);
                }
            }

            $order->save(false);

            \humhub\modules\shop\helpers\ShopNotify::notifySeller($order,
                'Order ' . $order->order_number . ' updated by buyer',
                '<p>The buyer has updated order <strong>' . $order->order_number . '</strong>.</p>'
            );

            Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Order updated.'));
            $this->view->saved();
            return $this->redirect(['/shop/store/my-orders']);
        }

        return $this->render('edit-order', ['order' => $order, 'addresses' => $addresses]);
    }

    /**
     * Buyer cancels order directly (only if unacknowledged).
     */
    public function actionCancelOrder($id)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Administrators cannot place or manage orders.'));
            return $this->redirect(['/shop/store/index']);
        }
        $this->forcePostRequest();
        $order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$order) throw new NotFoundHttpException();

        if ($order->status !== Order::STATUS_PAID) {
            Yii::$app->session->setFlash('error', 'This order can no longer be cancelled directly.');
            return $this->redirect(['/shop/store/my-orders']);
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save(false);

        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock !== null) {
                $item->product->updateCounters(['stock' => $item->quantity]);
            }
        }

        \humhub\modules\shop\helpers\ShopNotify::notifySeller($order,
            'Order ' . $order->order_number . ' cancelled by buyer',
            '<p>The buyer has cancelled order <strong>' . $order->order_number . '</strong>.</p>'
        );

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Order cancelled.'));
        $this->view->saved();
        return $this->redirect(['/shop/store/my-orders']);
    }

    /**
     * Buyer requests update/cancel for acknowledged (verified) orders.
     */
    public function actionRequestChange($id)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Administrators cannot place or manage orders.'));
            return $this->redirect(['/shop/store/index']);
        }
        $order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$order) throw new NotFoundHttpException();

        if (!in_array($order->status, [Order::STATUS_VERIFIED])) {
            return $this->redirect(['/shop/store/my-orders']);
        }

        $addresses = \humhub\modules\shop\models\DeliveryAddress::getForUser(Yii::$app->user->id);

        if (Yii::$app->request->isPost) {
            $req = new \humhub\modules\shop\models\OrderRequest();
            $req->order_id = $order->id;
            $req->user_id = Yii::$app->user->id;
            $req->type = Yii::$app->request->post('request_type', 'update');
            $req->details = Yii::$app->request->post('details', '');
            $req->new_address_id = Yii::$app->request->post('new_address_id') ?: null;
            $req->new_quantity = Yii::$app->request->post('new_quantity') ?: null;
            $req->save();

            \humhub\modules\shop\helpers\ShopNotify::notifySeller($order,
                'Change request for order ' . $order->order_number,
                '<p>The buyer has requested a ' . $req->type . ' for order <strong>' . $order->order_number . '</strong>.</p><p>' . \humhub\libs\Html::encode($req->details) . '</p>'
            );

            Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Request submitted.'));
            $this->view->saved();
            return $this->redirect(['/shop/store/my-orders']);
        }

        return $this->render('request-change', ['order' => $order, 'addresses' => $addresses]);
    }

    public function actionMyOrders()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) return $this->redirect(['/shop/admin/index']);

        $query = Order::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 15]);

        return $this->render('my-orders', [
            'orders' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
        ]);
    }

    public function actionToggleWishlist($productId)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) return $this->asJson(['error' => 'Admin accounts cannot use shop features.']);
        Yii::$app->response->format = 'json';
        $added = Wishlist::toggle(Yii::$app->user->id, (int) $productId);
        return ['wishlisted' => $added];
    }

    public function actionToggleFollow($vendorId)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) return $this->asJson(['error' => 'Admin accounts cannot use shop features.']);
        Yii::$app->response->format = 'json';
        $added = FavoriteStore::toggle(Yii::$app->user->id, (int) $vendorId);
        return ['following' => $added];
    }

    // Keep old action for backward compat
    public function actionToggleFavorite($vendorId)
    {
        return $this->actionToggleFollow($vendorId);
    }

    public function actionWishlist()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) return $this->redirect(['/shop/admin/index']);
        $items = Wishlist::find()->where(['user_id' => Yii::$app->user->id])->with('product')->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('wishlist', ['items' => $items]);
    }

    public function actionFavorites()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        if (Yii::$app->user->isAdmin()) return $this->redirect(['/shop/admin/index']);
        $items = FavoriteStore::find()->where(['user_id' => Yii::$app->user->id])->with('vendor')->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('favorites', ['items' => $items]);
    }

    public function actionVendorStore($id)
    {
        $vendor = \humhub\modules\shop\helpers\ShopCache::getVendor($id);
        if (!$vendor) throw new NotFoundHttpException();

        $tab = Yii::$app->request->get('tab', 'all');
        $categoryFilter = Yii::$app->request->get('category');
        $sort = Yii::$app->request->get('sort', 'default');
        $keyword = Yii::$app->request->get('q');
        $isFollowing = (!Yii::$app->user->isGuest && !Yii::$app->user->isAdmin())
            ? FavoriteStore::isFavorited(Yii::$app->user->id, $vendor->id) : false;

        $productQuery = Product::find()->where(['vendor_id' => $vendor->id, 'is_active' => 1]);

        if ($tab === 'sale') {
            $productQuery->andWhere(['not', ['sale_price' => null]])
                ->andWhere(['<', 'sale_price', new \yii\db\Expression('price')]);
        }
        if ($categoryFilter) {
            $productQuery->andWhere(['category_id' => $categoryFilter]);
        }
        if ($keyword) {
            $productQuery->andWhere(['or', ['like', 'name', $keyword], ['like', 'description', $keyword]]);
        }

        switch ($sort) {
            case 'price_asc': $productQuery->orderBy(['price' => SORT_ASC]); break;
            case 'price_desc': $productQuery->orderBy(['price' => SORT_DESC]); break;
            case 'newest': $productQuery->orderBy(['created_at' => SORT_DESC]); break;
            case 'name': $productQuery->orderBy(['name' => SORT_ASC]); break;
            default: $productQuery->orderBy(['sort_order' => SORT_ASC]); break;
        }

        $products = $productQuery->all();
        $categories = Category::find()->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC])->all();

        return $this->render('vendor-store', [
            'vendor' => $vendor,
            'products' => $products,
            'categories' => $categories,
            'activeTab' => $tab,
            'isFollowing' => $isFollowing,
            'sort' => $sort,
            'keyword' => $keyword,
            'categoryFilter' => $categoryFilter,
        ]);
    }
}
