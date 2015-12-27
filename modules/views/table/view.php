<?php

use dmstr\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
 * @var yii\web\View $this
 * @var febfeb\dynamicfield\modules\models\Table $model
 */

$this->title = 'Table Content of ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="giiant-crud table-view">

    <!-- menu buttons -->
    <p class='pull-left'>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . 'Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . 'New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p class="pull-right">
        <?= Html::a('<span class="glyphicon glyphicon-list"></span> ' . 'List Tables', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="clearfix"></div>

    <div class="panel panel-default">
        <div class="panel-body">
            <?php if(isset($dataProvider)) {
                $column = [
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            // using the column name as key, not mapping to 'id' like the standard generator
                            $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
                            $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                            return Url::toRoute($params);
                        },
                        'contentOptions' => ['nowrap' => 'nowrap', 'style'=>'text-align:center'],
                        //'buttonOptions' => ["class"=>"btn btn-danger"],
                        //'updateOptions' => ["class"=>"btn btn-danger"],
                    ]
                ];

                /* @var $searchModel \app\models\search\CobaBroh */
                foreach($searchModel->attributes() as $attribute){
                    \app\components\NodeLogger::sendLog($attribute);
                    $column[] = $attribute;
                }

                ?>
            <?= GridView::widget([
                'layout' => '{summary}{pager}{items}{pager}',
                'dataProvider' => $dataProvider,
                'pager' => [
                    'class' => yii\widgets\LinkPager::className(),
                    'firstPageLabel' => 'First',
                    'lastPageLabel' => 'Last'],
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                'headerRowOptions' => ['class' => 'x'],
                'columns' => $column,
            ]); ?>
            <?php }else{
                echo "Please Generate Model First or Click Here : <br>".Html::a("<i class='fa fa-check'></i> Generate Model", ["table/generate", "id" => $model->id], ["class"=>"btn btn-info"]);
            } ?>
        </div>
    </div>
</div>
