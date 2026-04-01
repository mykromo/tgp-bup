<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\FunctionalCategory;
use humhub\modules\stewardship\permissions\ManageFinances;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class CategoryController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    private function requireManage()
    {
        if (!$this->contentContainer->permissionManager->can(ManageFinances::class)) {
            throw new ForbiddenHttpException();
        }
    }

    public function actionIndex()
    {
        $this->requireManage();
        $categories = FunctionalCategory::getForSpace($this->contentContainer->id);

        return $this->render('index', [
            'categories' => $categories,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionCreate()
    {
        $this->requireManage();
        $model = new FunctionalCategory();
        $model->space_id = $this->contentContainer->id;
        $model->is_default = 0;
        $model->is_active = 1;
        $model->sort_order = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/category/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->requireManage();
        $model = $this->findCategory($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/category/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionToggle($id)
    {
        $this->requireManage();
        $this->forcePostRequest();
        $model = $this->findCategory($id);
        $model->is_active = $model->is_active ? 0 : 1;
        $model->save(false);
        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/stewardship/category/index'));
    }

    public function actionDelete($id)
    {
        $this->requireManage();
        $this->forcePostRequest();
        $model = $this->findCategory($id);

        if ($model->isDefault()) {
            throw new HttpException(403, Yii::t('StewardshipModule.base', 'Default categories cannot be deleted. You can disable them instead.'));
        }

        $model->delete();
        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/stewardship/category/index'));
    }

    private function findCategory(int $id): FunctionalCategory
    {
        $model = FunctionalCategory::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) throw new NotFoundHttpException();
        return $model;
    }
}
