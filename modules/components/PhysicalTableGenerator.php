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
     * Create new table
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
     * Drop table
     * @param $model
     * @throws \yii\db\Exception
     */
    public static function dropTable($model){
        if(self::tableExist($model->slug_name)) {
            $db = \Yii::$app->db;
            $command = $db->createCommand();
            $command->dropTable($model->slug_name)->execute();
        }
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

    public static function getSafeTableName($tableName){
        $actual_name = Util::slugifyToDbSafe($tableName);
        $original_name = $actual_name;

        $i = 1;
        while(self::tableExist($actual_name))
        {
            $actual_name = (string)$original_name."_".$i;
            $i++;
        }

        return $actual_name;
    }

    public static function tableExist($tableName){
        $sql = "SHOW TABLES LIKE '".$tableName."'";
        $db = \Yii::$app->db;
        $command = $db->createCommand($sql);
        $array = $command->queryAll();
        if(count($array) == 0){ return false; }
        return true;
    }

    public static function getSafeFieldName($tableName, $fieldName){
        if(self::tableExist($tableName)) {
            $table_name = Util::slugifyToDbSafe($tableName);
            $actual_name = Util::slugifyToDbSafe($fieldName);
            $original_name = $actual_name;

            $i = 1;
            while (self::fieldExist($table_name, $actual_name)) {
                $actual_name = (string)$original_name . "_" . $i;
                $i++;
            }

            return $actual_name;
        }else{
            return Util::slugifyToDbSafe($fieldName);
        }
    }

    public static function fieldExist($tableName, $fieldName){
        if(self::tableExist($tableName)){
            $sql = "DESC '".$tableName."'";
            $db = \Yii::$app->db;
            $command = $db->createCommand($sql);
            $array = $command->queryAll();
            foreach($array as $elem){
                if($elem["Field"] == $fieldName){
                    return false;
                }
            }
            return true;
        }else{
            return true;
        }
    }
}