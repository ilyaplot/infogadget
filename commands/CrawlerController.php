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

            $images = $product['info']['images'];
            unset($product['info']['images']);
            $model = new \app\models\Product();
            $attributes = [
                'title' => $product['title'],
                'brand_id' => $lenovo->brand_id,
                'product_type_id' => 1, // @todo Заменить
                'model' => $product['model'], // @todo Заменить
                'options' => \yii\helpers\Json::encode($product['info']), // @todo Заменить
            ];
            $model->setAttributes($attributes);
            $model->save();
            foreach ($images as $key => $image) {
                $tempnam = tempnam('/tmp/', 'image');
                try {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('get', $image);
                    if ($response->getBody()->isReadable()) {
                        if ($response->getStatusCode() == 200) {
                            file_put_contents($tempnam, $response->getBody()->getContents());

                            $file = new \app\models\File();
                            $file->hash = md5_file($tempnam);
                            $file->mime = mime_content_type($tempnam);
                            $file->size = filesize($tempnam);
                            $file->extension = preg_replace("/\w+\/(\w+)$/", "$1", $file->mime);

                            if ($file->save()) {
            
                                $dirname = \yii::$app->basePath . '/web/images/';
                                $dirname .= mb_substr($file->hash, 0, 2) . '/';
                                $dirname .= mb_substr($file->hash, 2, 2) . '/';
                                
                                if (!file_exists($dirname) || !is_dir($dirname)) {
                                    mkdir($dirname, 0777, true);
                                }
                                
                                $filename = $dirname . $file->hash . '.' . $file->extension;
                                rename($tempnam, $filename);
                                chmod($filename, 0777);
                                $imageProduct = new \app\models\ImageProduct();
                                $imageProduct->product_id = $model->id;
                                $imageProduct->file_id = $file->id;
                                $imageProduct->save();
                            }
                        }
                    }
                    @unlink($tempnam);
                } catch (Exception $ex) {
                    @unlink($tempnam);
                    throw $ex;
                }
            }
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
