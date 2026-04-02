<?php

namespace humhub\modules\shop\helpers;

use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\Vendor;
use humhub\modules\user\models\User;
use Yii;

class ShopNotify
{
    /**
     * Notify buyer about order status change.
     */
    public static function notifyBuyer(Order $order, string $subject, string $body): void
    {
        $user = $order->user;
        if (!$user) return;

        try {
            $originator = (!Yii::$app->user->isGuest) ? Yii::$app->user->getIdentity() : $user;
            $notification = new \humhub\modules\shop\notifications\OrderUpdate([
                'source' => $order,
                'originator' => $originator,
            ]);
            $notification->customMessage = $subject;
            $notification->send($user);
        } catch (\Throwable $e) {
            Yii::error('Shop notification failed: ' . $e->getMessage(), 'shop');
        }

        // Email
        static::sendEmail($user->email, $subject, $body);
    }

    /**
     * Notify seller about order activity.
     */
    public static function notifySeller(Order $order, string $subject, string $body): void
    {
        // Find the vendor for this order's products
        $item = $order->items[0] ?? null;
        if (!$item || !$item->product || !$item->product->vendor_id) return;

        $vendor = Vendor::findOne($item->product->vendor_id);
        if (!$vendor || !$vendor->user) return;

        try {
            $notification = new \humhub\modules\shop\notifications\OrderUpdate([
                'source' => $order,
                'originator' => $order->user,
            ]);
            $notification->customMessage = $subject;
            $notification->send($vendor->user);
        } catch (\Throwable $e) {
            Yii::error('Shop seller notification failed: ' . $e->getMessage(), 'shop');
        }

        static::sendEmail($vendor->user->email, $subject, $body);
    }

    /**
     * Notify both buyer and seller.
     */
    public static function notifyBoth(Order $order, string $buyerSubject, string $buyerBody, string $sellerSubject, string $sellerBody): void
    {
        static::notifyBuyer($order, $buyerSubject, $buyerBody);
        static::notifySeller($order, $sellerSubject, $sellerBody);
    }

    private static function sendEmail(string $to, string $subject, string $body): void
    {
        try {
            Yii::$app->mailer->compose()
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();
        } catch (\Throwable $e) {
            Yii::error('Shop email failed: ' . $e->getMessage(), 'shop');
        }
    }

    /**
     * Get messenger URL with pre-filled subject.
     */
    public static function getMessengerUrl(Order $order, int $recipientId): string
    {
        $itemName = ($order->items[0] ?? null) ? $order->items[0]->product_name : 'Order';
        $subject = $order->order_number . ' - ' . $itemName;
        $user = User::findOne($recipientId);
        $guid = $user ? $user->guid : '';

        return \yii\helpers\Url::to(['/mail/mail/create', 'userGuid' => $guid, 'title' => $subject]);
    }
}
