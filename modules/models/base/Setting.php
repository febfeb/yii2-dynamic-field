<?php

namespace febfeb\dynamicfield\modules\models\base;

use Yii;

/**
 * This is the base-model class for table "df_setting".
 *
 * @property integer $id
 * @property string $model_namespace
 * @property string $controller_namespace
 * @property string $view_path
 */
class Setting extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'df_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_namespace', 'controller_namespace', 'view_path'], 'required'],
            [['model_namespace', 'controller_namespace', 'view_path'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_namespace' => 'Model Namespace',
            'controller_namespace' => 'Controller Namespace',
            'view_path' => 'View Path',
        ];
    }




}
