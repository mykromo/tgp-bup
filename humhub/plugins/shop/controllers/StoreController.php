<?php

namespace humhub\modules\shop\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\OrderItem;
use humhub\modules\shop\models\PaymentSetting;
use humhub\modules\shop\models\Product;
use humhub\modules\space\models\Space;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class StoreController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        $spaceId = $this->contentContainer->id;
        $query = Product::find()->where(['space_id' => $spaceId, 'is_active' => 1])->orderBy(['sort_order' => SORT_ASC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 12]);

        return $this->render('index', [
            'products' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'canManage' => $this->contentContainer->permissionManager->can(\humhub\modules\shop\permissions\ManageShop::class),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionView($id)
    {
        $product = Product::findOne(['id' => $id, 'space_id' => $this->contentContainer->id, 'is_active' => 1]);
        if (!$product) throw new NotFoundHttpException();

        return $this->render('view', [
            'product' => $product,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionBuy($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        $product = Product::findOne(['id' => $id, 'space_id' => $this->contentContainer->id, 'is_active' => 1]);
        if (!$product || !$product->isInStock()) throw new NotFoundHttpException();

        $settings = PaymentSetting::getForSpace($this->contentContainer->id);
        $user = Yii::$app->user->getIdentity();
        $quantity = max(1, (int) Yii::$app->request->post('quantity', 1));

        if (Yii::$app->request->isPost && Yii::$app->request->post('confirm')) {
            $paymentRef = Yii::$app->request->post('payment_reference');
            $paymentMethod = Yii::$app->request->post('payment_method');

            if (empty($paymentRef)) {
                Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Payment reference number is required.'));
                return $this->redirect($this->contentContainer->createUrl('/shop/store/buy', ['id' => $id]));
            }

            $total = $product->price * $quantity;

            $order = new Order();
            $order->space_id = $this->contentContainer->id;
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

                return $this->redirect($this->contentContainer->createUrl('/shop/store/order-confirmation', ['id' => $order->id]));
            }
        }

        return $this->render('buy', [
            'product' => $product,
            'settings' => $settings,
            'user' => $user,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionOrderConfirmation($id)
    {
        $order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$order) throw new NotFoundHttpException();

        return $this->render('order-confirmation', [
            'order' => $order,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionMyOrders()
    {
        $query = Order::find()
            ->where(['space_id' => $this->contentContainer->id, 'user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 15]);

        return $this->render('my-orders', [
            'orders' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
