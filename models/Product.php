<?php
namespace app\models;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    
    public static function tableName()
    {
        return '{{product}}';
    }
    
    public function rules()
    {
        return [
            [['title', 'model', 'brand_id', 'product_type_id', 'options'], 'safe'],
        ];
    }
    
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }
    
    public function getType()
    {
        return $this->hasOne(ProductType::className(), ['id' => 'product_type_id']);
    }
    
    public function getOptions()
    {
        return \yii\helpers\Json::decode($this->options);
    }
}
