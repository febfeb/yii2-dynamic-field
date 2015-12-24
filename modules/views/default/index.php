<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var $this yii\web\View
 */

$this->title = 'Setting';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud table-update">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="setting-form">
                <?php $form = ActiveForm::begin([
                        'id' => 'Setting',
                        'layout' => 'horizontal',
                        'enableClientValidation' => true,
                        'errorSummaryCssClass' => 'error-summary alert alert-error'
                    ]
                );
                ?>

                <div class="">

                    <?= $form->field($model, 'model_namespace')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'controller_namespace')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'view_path')->textInput(['maxlength' => true]) ?>

                    <hr/>
                    <?php echo $form->errorSummary($model); ?>

                    <?= Html::submitButton(
                        '<span class="glyphicon glyphicon-check"></span> ' .
                        ($model->isNewRecord ? 'Create' : 'Save'),
                        [
                            'id' => 'save-' . $model->formName(),
                            'class' => 'btn btn-success'
                        ]
                    );
                    ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
