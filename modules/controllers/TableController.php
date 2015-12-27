<?php

namespace febfeb\dynamicfield\modules\controllers;

use app\components\NodeLogger;
use febfeb\dynamicfield\modules\components\PhysicalTableGenerator;
use febfeb\dynamicfield\modules\components\Util;
use febfeb\dynamicfield\modules\models\Field;
use febfeb\dynamicfield\modules\models\Setting;
use febfeb\dynamicfield\modules\models\Table;
use febfeb\dynamicfield\modules\models\search\TableSearch;
use febfeb\dynamicfield\modules\components\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use yii\helpers\Inflector;

/**
 * TableController implements the CRUD actions for Table model.
 */
class TableController extends Controller
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;
    public $layout = "main";

    /**
     * Lists all Table models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TableSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Table model.
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $table = $this->findModel($id);

        $searchModel = null;
        $dataProvider = null;

        if ($table->model_search_class != null) {
            $class = $table->model_search_class;
            $searchModel = new $class();
            $dataProvider = $searchModel->search($_GET);
        }

        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        return $this->render('view', [
            'model' => $table,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Table model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $modelTable = new Table();
        $modelsField = [new Field()];

        NodeLogger::sendLog("Init");

        if ($modelTable->load(\Yii::$app->request->post())) {
            NodeLogger::sendLog("Loaded Post Request");
            $modelTable->slug_name = PhysicalTableGenerator::getSafeTableName($modelTable->name);


            $modelsField = Model::createMultiple(Field::classname());
            Model::loadMultiple($modelsField, \Yii::$app->request->post());

            NodeLogger::sendLog("Loaded Data");

            for ($i = 0; $i < count($modelsField); $i++) {
                $modelsField[$i]->slug_name = PhysicalTableGenerator::getSafeFieldName($modelTable->slug_name, $modelsField[$i]->name);//Util::slugifyToDbSafe($modelsField[$i]->name)."_".\Yii::$app->security->generateRandomString(6);
                $modelsField[$i]->df_table_id = 1;
            }

            NodeLogger::sendLog("Set Relation ID");

            // validate all models
            $valid = $modelTable->validate();
            $valid = Model::validateMultiple($modelsField) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $modelTable->save(false)) {
                        foreach ($modelsField as $modelField) {
                            $modelField->df_table_id = $modelTable->id;
                            if (!($flag = $modelField->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        PhysicalTableGenerator::createTable($modelTable);
                        return $this->redirect(['view', 'id' => $modelTable->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'modelTable' => $modelTable,
            'modelsField' => (empty($modelsField)) ? [new Field()] : $modelsField
        ]);
    }

    /**
     * Updates an existing Table model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelTable = $this->findModel($id);
        $modelsField = $modelTable->fields;

        if ($modelTable->load(\Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsField, 'id', 'id');
            $modelsField = Model::createMultiple(Field::classname(), $modelsField);
            Model::loadMultiple($modelsField, \Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsField, 'id', 'id')));

            for ($i = 0; $i < count($modelsField); $i++) {
                if ($modelsField[$i]->isNewRecord) {
                    $modelsField[$i]->slug_name = PhysicalTableGenerator::getSafeFieldName($modelTable->slug_name, $modelsField[$i]->name);//Util::slugifyToDbSafe($modelsField[$i]->name) . "_" . \Yii::$app->security->generateRandomString(6);
                    $modelsField[$i]->df_table_id = $modelTable->id;
                }
            }

            // validate all models
            $valid = $modelTable->validate();
            $valid = Model::validateMultiple($modelsField) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelTable->save(false)) {
                        if (!empty($deletedIDs)) {
                            PhysicalTableGenerator::deleteField($modelTable, $deletedIDs);
                            Field::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsField as $modelField) {
                            $beforeIsNewRecord = $modelField->isNewRecord;
                            if (!($flag = $modelField->save(false))) {
                                $transaction->rollBack();
                                break;
                            } else {
                                if ($beforeIsNewRecord == true) {
                                    PhysicalTableGenerator::addField($modelTable, $modelField);
                                } else {
                                    PhysicalTableGenerator::updateField($modelTable, $modelField);
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['view', 'id' => $modelTable->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'modelTable' => $modelTable,
            'modelsField' => (empty($modelsField)) ? [new Field()] : $modelsField
        ]);
    }

    /**
     * Deletes an existing Table model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            if ($model != null) {
                PhysicalTableGenerator::dropTable($model);

                //delete model class and crud
                if ($model->model_class != null)
                    if (file_exists(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_class, '\\'))) . ".php"))
                        unlink(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_class, '\\'))) . ".php");
                if ($model->model_base_class != null)
                    if (file_exists(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_base_class, '\\'))) . ".php"))
                        unlink(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_base_class, '\\'))) . ".php");
                if ($model->model_search_class != null)
                    if (file_exists(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_search_class, '\\'))) . ".php"))
                        unlink(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->model_search_class, '\\'))) . ".php");
                if ($model->controller_class != null)
                    if (file_exists(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->controller_class, '\\'))) . ".php"))
                        unlink(\Yii::getAlias('@' . str_replace('\\', '/', ltrim($model->controller_class, '\\'))) . ".php");
                if ($model->view_path != null)
                    $this->deleteDirectory(\Yii::getAlias($model->view_path));
            }
            foreach ($model->fields as $field) {
                $field->delete();
            }
            $model->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        // TODO: improve detection
        $isPivot = strstr('$id', ',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = \Yii::$app->session['__crudReturnUrl'];
            \Yii::$app->session['__crudReturnUrl'] = null;

            return $this->redirect($url);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Generate Giiant Model and Giiant CRUD using physical table
     * @param integer $id
     * @throws HttpException
     */
    public function actionGenerate($id)
    {
        $table = $this->findModel($id);
        $setting = Setting::find()->where(["id" => 1])->one();

        //Generate Model

        $params = [
            'interactive' => false,
            'overwrite' => true,
            'template' => "default",
            'ns' => $setting->model_namespace,
            'db' => "db",
            'tableName' => $table->slug_name,
            'tablePrefix' => "",
            'enableI18N' => false,
            'singularEntities' => true,
            'messageCategory' => "app",
            'generateModelClass' => false,
            'baseClassSuffix' => "",
            'modelClass' => Inflector::camelize($table->slug_name),
            'baseClass' => "yii\db\ActiveRecord",
            'baseTraits' => null,
            'tableNameMap' => [],
            'generateQuery' => false,
            'queryNs' => 'app\models\query',
            'queryBaseClass' => 'yii\db\ActiveQuery',
        ];
        $route = 'gii/giiant-model';

        $app = \Yii::$app;
        $config = $GLOBALS['config'];
        unset($config["components"]["errorHandler"]);
        unset($config["components"]["user"]);
        $temp = new \yii\console\Application($config);
        $temp->runAction(ltrim($route, '/'), $params);
        unset($temp);
        \Yii::$app = $app;

        $table->model_class = $setting->model_namespace . "\\" . Inflector::camelize($table->slug_name);
        $table->model_base_class = $setting->model_namespace . "\\base\\" . Inflector::camelize($table->slug_name);
        $table->save();

        //Generate CRUD
        $this->createDirectoryFromNamespace($setting->controller_namespace);
        $this->createDirectoryFromNamespace($setting->model_namespace . "\\search");

        $name = Inflector::camelize($table->slug_name);
        $params = [
            'interactive' => false,
            'overwrite' => true,
            'template' => "default",
            'modelClass' => $setting->model_namespace . '\\' . $name,
            'searchModelClass' => $setting->model_namespace . '\\search\\' . $name,
            'controllerNs' => $setting->controller_namespace,
            'controllerClass' => $setting->controller_namespace . '\\' . $name . 'Controller',
            'viewPath' => $setting->view_path,
            'pathPrefix' => "",
            'tablePrefix' => "",
            'enableI18N' => false,
            'singularEntities' => true,
            'messageCategory' => "app",
            'actionButtonClass' => "yii\\grid\\ActionColumn",
            'baseControllerClass' => "yii\\web\\Controller",
            'providerList' => [],
            'skipRelations' => [],
            'accessFilter' => true,
            'tidyOutput' => true,
        ];
        $route = 'gii/giiant-crud';
        $app = \Yii::$app;
        $temp = new \yii\console\Application($config);
        $temp->runAction(ltrim($route, '/'), $params);
        unset($temp);
        \Yii::$app = $app;
        \Yii::$app->log->logger->flush(true);

        $table->controller_class = $setting->controller_namespace . '\\' . $name . 'Controller';
        $table->view_path = $setting->view_path . "/" . Util::slugify($table->slug_name);
        $table->save();

        \Yii::$app->session->addFlash("success", "Generate Success.");

        $this->redirect(["table/index"]);
    }

    /**
     * Helper function to create
     *
     * @param $ns Namespace
     */
    private function createDirectoryFromNamespace($ns)
    {
        echo \Yii::getRootAlias($ns);
        $dir = \Yii::getAlias('@' . str_replace('\\', '/', ltrim($ns, '\\')));
        @mkdir($dir);
    }

    /**
     * Helper function to delete directory
     *
     * @param $dir string
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    /**
     * Finds the Table model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Table the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Table::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
}
