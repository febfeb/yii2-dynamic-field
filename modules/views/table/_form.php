<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var yii\web\View $this */
/* @var febfeb\dynamicfield\modules\models\Table $modelTable */
/* @var febfeb\dynamicfield\modules\models\Field $modelsField */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Field: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Field: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($modelTable, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="padding-v-md">
            <div class="line line-dashed"></div>
        </div>
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 4, // the maximum times, an element can be cloned (default 999)
            'min' => 0, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelsField[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'full_name',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
            ],
        ]); ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-envelope"></i> Address Book
                <button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i> Add
                    address
                </button>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body container-items"><!-- widgetContainer -->
                <?php foreach ($modelsField as $index => $modelField): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <span class="panel-title-address">Address: <?= ($index + 1) ?></span>
                            <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i
                                    class="fa fa-minus"></i></button>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                            // necessary for update action.
                            if (!$modelField->isNewRecord) {
                                echo Html::activeHiddenInput($modelField, "[{$index}]id");
                            }

                            /* var $form ActiveForm; */
                            $basicItems = \febfeb\dynamicfield\modules\components\PhysicalTableGenerator::getType();
                            $dbItems = \yii\helpers\ArrayHelper::map(\febfeb\dynamicfield\modules\models\Table::find()->where("id != '{$modelTable->id}'")->all(), "slug_name", "name");
                            $items = array_merge($basicItems, $dbItems);
                            ?>

                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($modelField, "[{$index}]name")->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelField, "[{$index}]type")->dropDownList($items) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php DynamicFormWidget::end(); ?>

        <div class="form-group">
            <?= Html::submitButton($modelField->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>