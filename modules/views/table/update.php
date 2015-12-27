<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var febfeb\dynamicfield\modules\models\Table $model
 */

$this->title = 'Table ' . $modelTable->name . ', ' . 'Edit';
$this->params['breadcrumbs'][] = ['label' => 'Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$modelTable->name, 'url' => ['view', 'id' => $modelTable->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="giiant-crud table-update">

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . 'View', ['view', 'id' => $modelTable->id], ['class' => 'btn btn-default']) ?>
    </p>

	<?php echo $this->render('_form', [
        'modelTable' => $modelTable,
        'modelsField' => $modelsField,
	]); ?>

</div>
