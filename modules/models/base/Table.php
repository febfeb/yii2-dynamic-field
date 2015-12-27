<?php

namespace febfeb\dynamicfield\modules\models\base;

use Yii;

/**
 * This is the base-model class for table "df_table".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug_name
 * @property string $model_class
 * @property string $model_base_class
 * @property string $model_search_class
 * @property string $controller_class
 * @property string $view_path
 *
 * @property \febfeb\dynamicfield\modules\models\Field[] $fields
 */
class Table extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'df_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug_name'], 'required'],
            [['name', 'slug_name'], 'string', 'max' => 50],
            [['model_class', 'model_base_class', 'model_search_class', 'controller_class', 'view_path'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug_name' => 'Slug Name',
            'model_class' => 'Model Class',
            'model_base_class' => 'Model Base Class',
            'model_search_class' => 'Model Search Class',
            'controller_class' => 'Controller Class',
            'view_path' => 'View Path',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(\febfeb\dynamicfield\modules\models\Field::className(), ['df_table_id' => 'id']);
    }




}
