<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

class ProductImage extends ActiveRecord
{
    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 800;
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public static function tableName() { return 'shop_product_image'; }

    public function rules()
    {
        return [
            [['product_id', 'file_name', 'file_path'], 'required'],
            [['product_id', 'file_size', 'sort_order'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 500],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getUrl(): string
    {
        return Yii::getAlias('@web') . '/' . $this->file_path;
    }

    public static function getUploadPath(): string
    {
        $path = Yii::getAlias('@webroot/uploads/shop/products');
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
        return $path;
    }

    /**
     * Resize image to fit within max dimensions while keeping aspect ratio.
     */
    public static function resizeImage(string $sourcePath, string $destPath, int $maxW = self::MAX_WIDTH, int $maxH = self::MAX_HEIGHT): bool
    {
        $info = getimagesize($sourcePath);
        if (!$info) return false;

        list($origW, $origH, $type) = $info;

        // No resize needed if already within limits
        if ($origW <= $maxW && $origH <= $maxH) {
            if ($sourcePath !== $destPath) {
                copy($sourcePath, $destPath);
            }
            return true;
        }

        $ratio = min($maxW / $origW, $maxH / $origH);
        $newW = (int) round($origW * $ratio);
        $newH = (int) round($origH * $ratio);

        $creators = [
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
        ];
        if (defined('IMAGETYPE_WEBP')) {
            $creators[IMAGETYPE_WEBP] = 'imagecreatefromwebp';
        }

        $src = isset($creators[$type]) ? $creators[$type]($sourcePath) : null;

        if (!$src) return false;

        $dst = imagecreatetruecolor($newW, $newH);

        // Preserve transparency for PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        $result = false;
        if ($type === IMAGETYPE_JPEG) {
            $result = imagejpeg($dst, $destPath, 85);
        } elseif ($type === IMAGETYPE_PNG) {
            $result = imagepng($dst, $destPath, 8);
        } elseif (defined('IMAGETYPE_WEBP') && $type === IMAGETYPE_WEBP && function_exists('imagewebp')) {
            $result = imagewebp($dst, $destPath, 85);
        }

        imagedestroy($src);
        imagedestroy($dst);

        return $result;
    }
}
