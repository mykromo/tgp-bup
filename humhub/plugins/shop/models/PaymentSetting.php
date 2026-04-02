<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use Yii;

class PaymentSetting extends ActiveRecord
{
    public static function tableName() { return 'shop_payment_setting'; }

    public function rules()
    {
        return [
            [['space_id'], 'required'],
            [['space_id'], 'integer'],
            [['payment_instructions'], 'string'],
            [['accepted_methods'], 'string', 'max' => 500],
        ];
    }

    public static function getForSpace(int $spaceId): self
    {
        $setting = static::findOne(['space_id' => $spaceId]);
        if (!$setting) {
            $setting = new static();
            $setting->space_id = $spaceId;
            $setting->payment_instructions = "Please send payment via GCash, bank transfer, or cash.\nInclude your Order Number as reference.\nOnce paid, submit your payment reference number.";
            $setting->accepted_methods = 'GCash,Bank Transfer,Cash';
            $setting->save(false);
        }
        return $setting;
    }

    public static function getGlobal(): self
    {
        $setting = static::find()->where(['space_id' => null])->one();
        if (!$setting) {
            $setting = new static();
            $setting->space_id = null;
            $setting->payment_instructions = "Please send payment via GCash, bank transfer, or cash.\nInclude your Order Number as reference.\nOnce paid, submit your payment reference number.";
            $setting->accepted_methods = 'GCash,Bank Transfer,Cash';
            $setting->save(false);
        }
        return $setting;
    }

    public function getMethodsList(): array
    {
        return array_map('trim', explode(',', $this->accepted_methods ?? ''));
    }
}
