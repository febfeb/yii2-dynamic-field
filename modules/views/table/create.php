<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var febfeb\dynamicfield\modules\models\Table $model
*/

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud table-create">

    <p class="pull-left">
        <?= Html::a('Cancel', \yii\helpers\Url::previous(), ['class' => 'btn btn-default']) ?>
    </p>
    <div class="clearfix"></div>

    <?= $this->render('_form', [
        'modelTable' => $modelTable,
        'modelsField' => $modelsField,
    ]); ?>

</div>
