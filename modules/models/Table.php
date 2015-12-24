<?php

namespace febfeb\dynamicfield\modules\models;

use Yii;
use \febfeb\dynamicfield\modules\models\base\Table as BaseTable;
use yii\helpers\Html;

/**
 * This is the model class for table "df_table".
 */
class Table extends BaseTable
{
    public function getGenerate(){
        return Html::a("<i class='fa fa-check'></i> Generate Model", ["table/generate", "id" => $this->id], ["class"=>"btn btn-info"]);
    }
}
