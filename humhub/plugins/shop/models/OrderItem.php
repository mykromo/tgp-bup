<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class OrderItem extends ActiveRecord
{
    public static function tableName() { return 'shop_order_item'; }

    public function rules()
    {
        return [
            [['order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            [['product_name'], 'string', 'max' => 255],
            [['unit_price', 'total_price'], 'number'],
        ];
    }

    public function getOrder(): ActiveQuery { return $this->hasOne(Order::class, ['id' => 'order_id']); }
    public function getProduct(): ActiveQuery { return $this->hasOne(Product::class, ['id' => 'product_id']); }
}
