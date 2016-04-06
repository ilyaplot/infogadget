<?php

namespace app\commands;

use yii\console\Controller;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class CrawlerController extends Controller
{

    public function actionIndex()
    {
        $lenovo = new \app\components\SourceParser();
        foreach ($lenovo->getProducts() as $product) {
            $model = new \app\models\Product();
            $attributes = [
                'title' => $product['title'],
                'brand_id' => $lenovo->brand_id,
                'product_type_id' => 1, // @todo Заменить
                'options' => \yii\helpers\Json::encode($product['info']), // @todo Заменить
            ];
            $model->setAttributes($attributes);
            $model->save();
        }
    }
    
    public function actionSamsung()
    {
        $lenovo = new \app\components\SamsungParser();
        foreach ($lenovo->getProducts() as $product) {
            $model = new \app\models\Product();
            $attributes = [
                'title' => $product['title'],
                'brand_id' => $lenovo->brand_id,
                'product_type_id' => 2, // @todo Заменить
                'model' => $product['model'], // @todo Заменить
                'options' => \yii\helpers\Json::encode($product['info']), // @todo Заменить
            ];
            $model->setAttributes($attributes);
            $model->save();
        }
    }

    /**
      public function actionIndex()
      {
      $client = new Client([
      'base_uri' => 'http://www.gsmarena.com/'
      ]);

      $proxy = \app\models\Proxy::find()->one();

      $result = $client->request('GET', '/makers.php3', [
      'proxy' => [
      'http' => $proxy->string,
      ]
      ]);

      $html = $result->getBody()->getContents();

      $crawler = new Crawler($html);

      $brands = $crawler->filter('img[width=92][height=22]')->each(function ($node, $i) {
      return [
      $node->attr('src'),
      $node->attr('alt'),
      $node->parents()->attr('href'),
      ];
      });

      var_dump($brands);
      }
     * 
     */
    public function actionProxy()
    {
        $client = new Client([
            'base_uri' => 'http://shop.lenovo.com/'
        ]);

        $proxy = \app\models\Proxy::find()->orderBy('rand()')->one();


        $result = $client->request('GET', '/ru/ru/smartphones/?menu-id=Смартфоны'/*                 * , [
                  'proxy' => [
                  'http' => $proxy->string,
                  ]
                  ]* */);


        $html = $result->getBody()->getContents();


        $crawler = new Crawler($html);

        $smartphones = $crawler->filter('li.subSeriesLink')->each(function ($node, $i) {
            return [
                $node->filter('a')->attr('href'),
                $node->filter('a')->text(),
            ];
        });

        var_dump($smartphones);
    }

}
