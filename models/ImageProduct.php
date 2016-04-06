<?php

namespace app\models;

use yii\db\ActiveRecord;

class ImageProduct extends ActiveRecord
{

    public static function tableName()
    {
        return '{{image_product}}';
    }

    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id'])->one();
    }

    public function getUri()
    {
        $uri = '/images/';
        $uri .= mb_substr($this->getFile()->hash, 0, 2) . '/';
        $uri .= mb_substr($this->getFile()->hash, 2, 2) . '/';
        $uri .= $this->getFile()->hash . '.' . $this->getFile()->extension;
        return $uri;
    }

}
