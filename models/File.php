<?php
namespace app\models;
use yii\db\ActiveRecord;

class File extends ActiveRecord
{
    
    public static function tableName()
    {
        return '{{file}}';
    }
}
