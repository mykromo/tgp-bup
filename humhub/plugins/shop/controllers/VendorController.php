<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\Vendor;
use humhub\modules\shop\models\VendorDocument;
use Yii;
use yii\web\UploadedFile;

class VendorController extends Controller
{
    public function init()
    {
        parent::init();
        $this->subLayout = '@shop/views/layouts/shop';
    }

    /**
     * Apply to become a vendor — any logged-in user
     */
    public function actionApply()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        // Administrators cannot apply for store creation — admin is for management only
        if (Yii::$app->user->isAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Administrators cannot apply for store creation. Admin accounts are for store management only.'));
            return $this->redirect(['/shop/store/index']);
        }

        $userId = Yii::$app->user->id;
        $existing = Vendor::getForUser($userId);

        if ($existing && $existing->isApproved()) {
            return $this->redirect(['/shop/store/index']);
        }

        if ($existing && $existing->isPending()) {
            return $this->render('pending', ['vendor' => $existing]);
        }

        $vendor = $existing ?: new Vendor();
        $vendor->user_id = $userId;

        if ($vendor->load(Yii::$app->request->post()) && $vendor->validate()) {
            $vendor->status = Vendor::STATUS_PENDING;
            $vendor->save(false);

            // Handle document uploads
            $docTypes = Vendor::getRequiredDocuments();
            foreach (array_keys($docTypes) as $type) {
                $file = UploadedFile::getInstanceByName("doc_{$type}");
                if ($file) {
                    $fileName = $vendor->id . '_' . $type . '_' . time() . '.' . $file->extension;
                    $filePath = VendorDocument::getUploadPath() . '/' . $fileName;
                    if ($file->saveAs($filePath)) {
                        $doc = new VendorDocument();
                        $doc->vendor_id = $vendor->id;
                        $doc->document_type = $type;
                        $doc->file_name = $file->name;
                        $doc->file_path = 'uploads/shop/vendor-docs/' . $fileName;
                        $doc->file_size = $file->size;
                        $doc->created_at = date('Y-m-d H:i:s');
                        $doc->save(false);
                    }
                }
            }

            return $this->render('pending', ['vendor' => $vendor]);
        }

        return $this->render('apply', [
            'vendor' => $vendor,
            'requiredDocs' => Vendor::getRequiredDocuments(),
        ]);
    }

    /**
     * My vendor status page
     */
    public function actionStatus()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);

        $vendor = Vendor::getForUser(Yii::$app->user->id);
        if (!$vendor) return $this->redirect(['/shop/vendor/apply']);

        return $this->render('status', ['vendor' => $vendor]);
    }

    /**
     * Suspended vendor requests re-enablement with explanation.
     */
    public function actionRequestReenable()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);

        $vendor = Vendor::find()
            ->where(['user_id' => Yii::$app->user->id, 'status' => Vendor::STATUS_SUSPENDED])
            ->one();

        if (!$vendor) {
            return $this->redirect(['/shop/vendor/status']);
        }

        if (Yii::$app->request->isPost) {
            $explanation = Yii::$app->request->post('explanation', '');
            if (empty(trim($explanation))) {
                Yii::$app->session->setFlash('error', Yii::t('ShopModule.base', 'Please provide an explanation.'));
                return $this->redirect(['/shop/vendor/request-reenable']);
            }

            $vendor->reenable_request = $explanation;
            $vendor->reenable_requested_at = date('Y-m-d H:i:s');
            $vendor->save(false);

            // Handle document uploads
            $files = \yii\web\UploadedFile::getInstancesByName('reenable_docs');
            foreach ($files as $file) {
                if ($file->size > 5 * 1024 * 1024) continue;
                $fileName = $vendor->id . '_reenable_' . time() . '_' . mt_rand(100, 999) . '.' . $file->extension;
                $uploadPath = \humhub\modules\shop\models\VendorDocument::getUploadPath();
                if ($file->saveAs($uploadPath . '/' . $fileName)) {
                    $doc = new \humhub\modules\shop\models\VendorDocument();
                    $doc->vendor_id = $vendor->id;
                    $doc->document_type = 'reenable_proof';
                    $doc->file_name = $file->name;
                    $doc->file_path = 'uploads/shop/vendor-docs/' . $fileName;
                    $doc->file_size = $file->size;
                    $doc->notes = 'Re-enable request proof';
                    $doc->created_at = date('Y-m-d H:i:s');
                    $doc->save(false);
                }
            }

            // Notify admins
            try {
                $admins = \humhub\modules\user\models\User::find()
                    ->innerJoin('group_user', 'group_user.user_id = user.id')
                    ->innerJoin('`group`', '`group`.id = group_user.group_id')
                    ->where(['group.is_admin_group' => 1])
                    ->all();

                foreach ($admins as $admin) {
                    \humhub\modules\shop\helpers\ShopNotify::sendEmail(
                        $admin->email,
                        'Store re-enable request: ' . $vendor->shop_name,
                        '<p><strong>' . \humhub\libs\Html::encode($vendor->user->displayName) . '</strong> has requested to re-enable their store <strong>' . \humhub\libs\Html::encode($vendor->shop_name) . '</strong>.</p>'
                        . '<p>Explanation: ' . \humhub\libs\Html::encode($explanation) . '</p>'
                    );
                }
            } catch (\Throwable $e) {
                Yii::error('Re-enable notification failed: ' . $e->getMessage(), 'shop');
            }

            Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Your request has been submitted. An administrator will review it.'));
            return $this->redirect(['/shop/vendor/status']);
        }

        return $this->render('request-reenable', ['vendor' => $vendor]);
    }
}
