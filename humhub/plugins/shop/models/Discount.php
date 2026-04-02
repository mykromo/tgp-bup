<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use Yii;

class Discount extends ActiveRecord
{
    public static function tableName() { return 'shop_discount'; }

    public function rules()
    {
        return [
            [['code', 'type', 'value'], 'required'],
            [['code'], 'string', 'max' => 50],
            [['code'], 'unique'],
            [['type'], 'in', 'range' => ['percentage', 'fixed']],
            [['value', 'min_order'], 'number', 'min' => 0],
            [['vendor_id', 'max_uses', 'used_count'], 'integer'],
            [['starts_at', 'expires_at'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    public function isValid(float $orderTotal): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($this->starts_at && strtotime($this->starts_at) > time()) return false;
        if ($this->expires_at && strtotime($this->expires_at) < time()) return false;
        if ($this->min_order && $orderTotal < $this->min_order) return false;
        return true;
    }

    public function calculateDiscount(float $total): float
    {
        if ($this->type === 'percentage') {
            return round($total * ($this->value / 100), 2);
        }
        return min((float) $this->value, $total);
    }
}
