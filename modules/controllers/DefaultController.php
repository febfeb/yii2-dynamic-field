<?php

namespace febfeb\dynamicfield\modules\controllers;

use febfeb\dynamicfield\modules\models\Setting;
use yii\helpers\Url;
use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = "main";

    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $model = $this->findModel(1);

        if ($model->load($_POST) && $model->save()) {
            \Yii::$app->session->addFlash("success", "Setting updated.");
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Table model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Setting the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
}
