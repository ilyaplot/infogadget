<?php

namespace app\models;

use yii\db\ActiveRecord;

class Proxy extends ActiveRecord
{

    public static function tableName()
    {
        return '{{proxy}}';
    }

    public function rules()
    {
        return [
            [['address', 'port'], 'required'],
            [['address', 'port'], 'safe'],
            ['address', 'unique', 'targetAttribute' => ['address', 'port']]
        ];
    }

    public static function import($list)
    {
        $successCount = 0;
        foreach ($list as $proxy) {
            $model = new Proxy();
            $model->setAttributes($proxy);
            if ($model->save(true)) {
                $successCount++;
            }
        }
        return $successCount;
    }

    public function getIp()
    {
        return long2ip($this->address);
    }

    public function getString()
    {
        return "tcp://" . $this->ip . ":" . $this->port;
    }

}
