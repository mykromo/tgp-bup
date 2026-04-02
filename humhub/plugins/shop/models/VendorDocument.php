<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

class VendorDocument extends ActiveRecord
{
    public $file;

    public static function tableName() { return 'shop_vendor_document'; }

    public function rules()
    {
        return [
            [['vendor_id', 'document_type', 'file_name', 'file_path'], 'required'],
            [['vendor_id', 'file_size'], 'integer'],
            [['document_type'], 'string', 'max' => 100],
            [['file_name'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 500],
            [['notes'], 'string'],
            [['file'], 'file', 'extensions' => 'jpg,jpeg,png,pdf,doc,docx', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function getVendor(): ActiveQuery { return $this->hasOne(Vendor::class, ['id' => 'vendor_id']); }

    public static function getUploadPath(): string
    {
        $path = \Yii::getAlias('@webroot/uploads/shop/vendor-docs');
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
        return $path;
    }
}
