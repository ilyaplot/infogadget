<?php
namespace app\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord
{
    
    public static function tableName()
    {
        return '{{brand}}';
    }
}
