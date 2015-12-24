<?php
/**
 * Created by PhpStorm.
 * User: feb
 * Date: 23/12/15
 * Time: 10.44
 */

namespace febfeb\dynamicfield\modules\components;


use app\components\NodeLogger;
use febfeb\dynamicfield\modules\models\Field;

class PhysicalTableGenerator
{
    /**
     * @param $model \febfeb\dynamicfield\modules\models\Table
     */
    public static function createTable($model){
        NodeLogger::sendLog("Create Table");
        $db = \Yii::$app->db;
        $command = $db->createCommand();

        $columns = [
            "id" => "int NOT NULL AUTO_INCREMENT PRIMARY KEY"
        ];
        foreach($model->fields as $field){
            $columns[$field->slug_name] = self::convertTypeToDbType($field);
        }

        $command->createTable($model->slug_name, $columns)->execute();

        NodeLogger::sendLog($columns);
    }

    /**
     * @param $model \febfeb\dynamicfield\modules\models\Table
     * @param $field_ids array
     */
    public static function deleteField($model, $field_ids){
        foreach($field_ids as $field_id){
            $field = Field::find()->where(["id"=>$field_id])->one();
            $db = \Yii::$app->db;
            $command = $db->createCommand();
            $command->dropColumn($model->slug_name, $field->slug_name)->execute();
        }
    }

    /**
     * @param $model \febfeb\dynamicfield\modules\models\Table
     * @param $field \febfeb\dynamicfield\modules\models\Field
     */
    public static function addField($model, $field){
        $db = \Yii::$app->db;
        $command = $db->createCommand();
        $command->addColumn($model->slug_name, $field->slug_name, self::convertTypeToDbType($field))->execute();
    }

    /**
     * @param $model \febfeb\dynamicfield\modules\models\Table
     * @param $field \febfeb\dynamicfield\modules\models\Field
     */
    public static function updateField($model, $field){
        $db = \Yii::$app->db;
        $command = $db->createCommand();
        $command->alterColumn($model->slug_name, $field->slug_name, self::convertTypeToDbType($field))->execute();
    }

    /**
     * @param $field \febfeb\dynamicfield\modules\models\Field
     */
    private static function convertTypeToDbType($field){
        if($field->relation_to){
            //there is relation
            return "int";
        }else{
            //no relation
            switch($field->type){
                case "combobox":
                    return "varchar(255) null";
                case "number":
                    return "float null";
                case "text":
                    return "text null";
                case "date":
                    return "date null";

                default :
                    return "text null";
            }
        }
    }

    public static function getType(){
        return [
            "combobox" => "Combobox",
            "number" => "Number",
            "text" => "Text",
            "date" => "Date"
        ];
    }
}