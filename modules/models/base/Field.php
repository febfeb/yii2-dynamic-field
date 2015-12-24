<?php

namespace febfeb\dynamicfield\modules\models\base;

use Yii;

/**
 * This is the base-model class for table "df_field".
 *
 * @property integer $id
 * @property integer $df_table_id
 * @property string $name
 * @property string $slug_name
 * @property string $type
 * @property string $relation_to
 * @property integer $order
 *
 * @property \febfeb\dynamicfield\modules\models\Table $dfTable
 */
class Field extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'df_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['df_table_id', 'name', 'slug_name', 'type'], 'required'],
            [['df_table_id', 'order'], 'integer'],
            [['name', 'slug_name', 'relation_to'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'df_table_id' => 'Df Table ID',
            'name' => 'Name',
            'slug_name' => 'Slug Name',
            'type' => 'Type',
            'relation_to' => 'Relation To',
            'order' => 'Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDfTable()
    {
        return $this->hasOne(\febfeb\dynamicfield\modules\models\Table::className(), ['id' => 'df_table_id']);
    }




}
