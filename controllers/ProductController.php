<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ProductController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $types = \app\models\ProductType::find()->all();
        return $this->render('types', [
            'types'=>$types
        ]);
    }
    
    public function actionType($id)
    {
        $products = \app\models\Product::find()->andWhere('product_type_id = :id', ['id'=>$id])->all();
        return $this->render('list', [
            'products'=>$products
        ]);
    }
}
