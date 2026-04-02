<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use Yii;

class DeliveryAddress extends ActiveRecord
{
    public static function tableName() { return 'shop_address'; }

    public function rules()
    {
        return [
            [['user_id', 'label', 'recipient_name', 'address_line1', 'city', 'country'], 'required'],
            [['user_id'], 'integer'],
            [['label'], 'string', 'max' => 100],
            [['recipient_name', 'address_line1', 'address_line2'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['city', 'province', 'country'], 'string', 'max' => 100],
            [['postal_code'], 'string', 'max' => 20],
            [['is_default'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'label' => Yii::t('ShopModule.base', 'Address Label'),
            'recipient_name' => Yii::t('ShopModule.base', 'Recipient Name'),
            'phone' => Yii::t('ShopModule.base', 'Phone'),
            'address_line1' => Yii::t('ShopModule.base', 'Address Line 1'),
            'address_line2' => Yii::t('ShopModule.base', 'Address Line 2'),
            'city' => Yii::t('ShopModule.base', 'City'),
            'province' => Yii::t('ShopModule.base', 'Province'),
            'postal_code' => Yii::t('ShopModule.base', 'Postal Code'),
            'country' => Yii::t('ShopModule.base', 'Country'),
            'is_default' => Yii::t('ShopModule.base', 'Default Address'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
        if ($this->is_default) {
            static::updateAll(['is_default' => 0], ['and', ['user_id' => $this->user_id], ['!=', 'id', $this->id ?: 0]]);
        }
        return parent::beforeSave($insert);
    }

    public static function getForUser(int $userId): array
    {
        return static::find()->where(['user_id' => $userId])->orderBy(['is_default' => SORT_DESC, 'id' => SORT_DESC])->all();
    }

    public static function getDefaultForUser(int $userId): ?self
    {
        return static::find()->where(['user_id' => $userId, 'is_default' => 1])->one()
            ?: static::find()->where(['user_id' => $userId])->orderBy(['id' => SORT_DESC])->one();
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([$this->address_line1, $this->address_line2, $this->city, $this->province, $this->postal_code, $this->country]);
        return implode(', ', $parts);
    }

    public function getDropdownLabel(): string
    {
        return $this->label . ' — ' . $this->recipient_name . ', ' . $this->city;
    }
}
