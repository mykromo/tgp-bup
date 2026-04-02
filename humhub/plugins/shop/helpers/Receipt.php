<?php

namespace humhub\modules\shop\helpers;

use humhub\modules\shop\models\Order;
use humhub\libs\Html;

class Receipt
{
    /**
     * Generate receipt HTML for an order.
     */
    public static function generateHtml(Order $order): string
    {
        $items = $order->items;
        $vendor = null;
        if (!empty($items) && $items[0]->product && $items[0]->product->vendor) {
            $vendor = $items[0]->product->vendor;
        }

        $itemRows = '';
        foreach ($items as $item) {
            $itemRows .= '<tr>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee">' . Html::encode($item->product_name) . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:center">' . $item->quantity . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right">₱' . number_format($item->unit_price, 2) . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right">₱' . number_format($item->total_price, 2) . '</td>'
                . '</tr>';
        }

        $html = '
<div style="font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;color:#333">
    <div style="background:#337ab7;color:#fff;padding:20px 24px;border-radius:4px 4px 0 0">
        <h2 style="margin:0;font-size:20px">Order Receipt</h2>
        <p style="margin:4px 0 0;opacity:.85;font-size:13px">' . Html::encode($order->order_number) . '</p>
    </div>

    <div style="border:1px solid #ddd;border-top:none;padding:24px;border-radius:0 0 4px 4px">

        <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
            <tr>
                <td style="padding:4px 0;width:50%;vertical-align:top">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Buyer</strong><br>
                    ' . Html::encode($order->buyer_name) . '<br>
                    <span style="color:#777;font-size:13px">' . Html::encode($order->buyer_email) . '</span>
                </td>
                <td style="padding:4px 0;width:50%;vertical-align:top;text-align:right">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Date</strong><br>
                    ' . date('F j, Y', strtotime($order->created_at)) . '<br>
                    <span style="color:#777;font-size:13px">' . date('g:i A', strtotime($order->created_at)) . '</span>
                </td>
            </tr>
        </table>';

        if ($vendor) {
            $html .= '
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
            <tr>
                <td style="padding:4px 0">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Store</strong><br>
                    ' . Html::encode($vendor->shop_name) . '
                </td>
            </tr>
        </table>';
        }

        if ($order->delivery_address) {
            $html .= '
        <div style="margin-bottom:20px">
            <strong style="font-size:12px;color:#999;text-transform:uppercase">Delivery Address</strong><br>
            <span style="font-size:13px">' . nl2br(Html::encode($order->delivery_address)) . '</span>
        </div>';
        }

        $html .= '
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
            <thead>
                <tr style="background:#f8f8f8">
                    <th style="padding:10px 12px;text-align:left;font-size:12px;color:#999;text-transform:uppercase;border-bottom:2px solid #ddd">Item</th>
                    <th style="padding:10px 12px;text-align:center;font-size:12px;color:#999;text-transform:uppercase;border-bottom:2px solid #ddd">Qty</th>
                    <th style="padding:10px 12px;text-align:right;font-size:12px;color:#999;text-transform:uppercase;border-bottom:2px solid #ddd">Price</th>
                    <th style="padding:10px 12px;text-align:right;font-size:12px;color:#999;text-transform:uppercase;border-bottom:2px solid #ddd">Total</th>
                </tr>
            </thead>
            <tbody>' . $itemRows . '</tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding:12px;text-align:right;font-weight:700;font-size:15px">Total</td>
                    <td style="padding:12px;text-align:right;font-weight:700;font-size:15px;color:#337ab7">' . $order->formatTotal() . '</td>
                </tr>
            </tfoot>
        </table>

        <table style="width:100%;border-collapse:collapse;background:#f8f8f8;border-radius:4px">
            <tr>
                <td style="padding:12px">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Payment Method</strong><br>
                    ' . Html::encode($order->payment_method) . '
                </td>
                <td style="padding:12px">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Reference Number</strong><br>
                    <code style="background:#eee;padding:2px 6px;border-radius:3px">' . Html::encode($order->payment_reference) . '</code>
                </td>
                <td style="padding:12px;text-align:right">
                    <strong style="font-size:12px;color:#999;text-transform:uppercase">Status</strong><br>
                    <span style="color:#337ab7;font-weight:600">Awaiting Verification</span>
                </td>
            </tr>
        </table>

        <p style="margin-top:20px;font-size:12px;color:#999;text-align:center">
            This is your order receipt for <strong>' . Html::encode($order->order_number) . '</strong>.<br>
            Please keep this for your records.
        </p>
    </div>
</div>';

        return $html;
    }

    /**
     * Generate a full standalone HTML page for download.
     */
    public static function generateDownloadHtml(Order $order): string
    {
        $receiptBody = static::generateHtml($order);
        return '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt - ' . Html::encode($order->order_number) . '</title>
<style>body{background:#f5f5f5;padding:20px}@media print{body{background:#fff;padding:0}}</style>
</head>
<body>' . $receiptBody . '</body>
</html>';
    }
}
