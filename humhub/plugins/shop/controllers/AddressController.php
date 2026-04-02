<?php

namespace humhub\modules\shop\controllers;

use humhub\components\Controller;
use humhub\modules\shop\models\DeliveryAddress;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AddressController extends Controller
{
    public function init()
    {
        parent::init();
        if ($this->module) {
            $this->subLayout = $this->module->getBasePath() . '/views/layouts/main';
        }
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        $addresses = DeliveryAddress::getForUser(Yii::$app->user->id);
        return $this->render('index', ['addresses' => $addresses]);
    }

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        $model = new DeliveryAddress();
        $model->user_id = Yii::$app->user->id;
        $model->country = 'Philippines';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            $returnUrl = Yii::$app->request->get('return');
            return $this->redirect($returnUrl ?: ['/shop/address/index']);
        }
        return $this->render('form', ['model' => $model]);
    }

    public function actionEdit($id)
    {
        if (Yii::$app->user->isGuest) return $this->redirect(Yii::$app->user->loginUrl);
        $model = DeliveryAddress::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect(['/shop/address/index']);
        }
        return $this->render('form', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest) throw new ForbiddenHttpException();
        $this->forcePostRequest();
        $model = DeliveryAddress::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($model) $model->delete();
        return $this->redirect(['/shop/address/index']);
    }
}
