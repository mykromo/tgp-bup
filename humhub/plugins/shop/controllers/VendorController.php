<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\Vendor;
use humhub\modules\shop\models\VendorDocument;
use Yii;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
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

    // ── Admin review actions ──

    public function actionApplications()
    {
        if (!Yii::$app->user->isAdmin()) throw new ForbiddenHttpException();

        $status = Yii::$app->request->get('status', 'pending');
        $query = Vendor::find()->where(['status' => $status])->orderBy(['created_at' => SORT_DESC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('applications', [
            'vendors' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'selectedStatus' => $status,
        ]);
    }

    public function actionReview($id)
    {
        if (!Yii::$app->user->isAdmin()) throw new ForbiddenHttpException();

        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        return $this->render('review', ['vendor' => $vendor]);
    }

    public function actionApprove($id)
    {
        if (!Yii::$app->user->isAdmin()) throw new ForbiddenHttpException();
        $this->forcePostRequest();

        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_APPROVED;
        $vendor->reviewed_by = Yii::$app->user->id;
        $vendor->reviewed_at = date('Y-m-d H:i:s');
        $vendor->save(false);

        // Send notification + email
        $notification = new \humhub\modules\shop\notifications\VendorApproved([
            'source' => $vendor,
            'originator' => Yii::$app->user->getIdentity(),
        ]);
        $notification->send($vendor->user);

        // Send email directly
        try {
            $mail = Yii::$app->mailer->compose()
                ->setTo($vendor->user->email)
                ->setSubject(Yii::t('ShopModule.base', 'Your shop application has been approved!'))
                ->setHtmlBody(
                    '<h3>' . Yii::t('ShopModule.base', 'Congratulations!') . '</h3>'
                    . '<p>' . Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been approved. You can now start adding products and selling on the platform.', ['shopName' => $vendor->shop_name]) . '</p>'
                    . '<p><a href="' . \yii\helpers\Url::to(['/shop/vendor/status'], true) . '">' . Yii::t('ShopModule.base', 'Go to your shop') . '</a></p>'
                );
            $mail->send();
        } catch (\Throwable $e) {
            Yii::error('Vendor approval email failed: ' . $e->getMessage(), 'shop');
        }

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Application approved.'));
        $this->view->saved();
        return $this->redirect(['/shop/vendor/applications']);
    }

    public function actionReject($id)
    {
        if (!Yii::$app->user->isAdmin()) throw new ForbiddenHttpException();
        $this->forcePostRequest();

        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_REJECTED;
        $vendor->rejection_reason = Yii::$app->request->post('reason', '');
        $vendor->reviewed_by = Yii::$app->user->id;
        $vendor->reviewed_at = date('Y-m-d H:i:s');
        $vendor->save(false);

        // Send notification + email
        $notification = new \humhub\modules\shop\notifications\VendorRejected([
            'source' => $vendor,
            'originator' => Yii::$app->user->getIdentity(),
        ]);
        $notification->send($vendor->user);

        try {
            $reason = $vendor->rejection_reason ? Yii::t('ShopModule.base', 'Reason: {reason}', ['reason' => $vendor->rejection_reason]) : '';
            $mail = Yii::$app->mailer->compose()
                ->setTo($vendor->user->email)
                ->setSubject(Yii::t('ShopModule.base', 'Your shop application has been rejected'))
                ->setHtmlBody(
                    '<h3>' . Yii::t('ShopModule.base', 'Application Update') . '</h3>'
                    . '<p>' . Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been rejected.', ['shopName' => $vendor->shop_name]) . '</p>'
                    . ($reason ? '<p>' . $reason . '</p>' : '')
                    . '<p><a href="' . \yii\helpers\Url::to(['/shop/vendor/apply'], true) . '">' . Yii::t('ShopModule.base', 'You may reapply with updated documents.') . '</a></p>'
                );
            $mail->send();
        } catch (\Throwable $e) {
            Yii::error('Vendor rejection email failed: ' . $e->getMessage(), 'shop');
        }

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Application rejected.'));
        $this->view->saved();
        return $this->redirect(['/shop/vendor/applications']);
    }

    public function actionSuspend($id)
    {
        if (!Yii::$app->user->isAdmin()) throw new ForbiddenHttpException();
        $this->forcePostRequest();

        $vendor = Vendor::findOne($id);
        if (!$vendor) throw new NotFoundHttpException();

        $vendor->status = Vendor::STATUS_SUSPENDED;
        $vendor->reviewed_by = Yii::$app->user->id;
        $vendor->reviewed_at = date('Y-m-d H:i:s');
        $vendor->save(false);

        Yii::$app->session->setFlash('success', Yii::t('ShopModule.base', 'Vendor suspended.'));
        $this->view->saved();
        return $this->redirect(['/shop/vendor/applications']);
    }
}
