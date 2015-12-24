<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var febfeb\dynamicfield\modules\models\search\TableSearch $searchModel
 */

$this->title = 'Table';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud table-index">

    <?php //     echo $this->render('_search', ['model' =>$searchModel]);
    ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . 'New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>


    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-main', 'enableReplaceState' => false, 'linkSelector' => '#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">
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
                    'columns' => [
                        'name',
                        [
                            'attribute' => 'generate',
                            'format' => 'raw',
                        ],
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
                        ],
                        //'slug_name',
                    ],
                ]); ?>
            </div>

        </div>

    </div>

    <?php \yii\widgets\Pjax::end() ?>


</div>
