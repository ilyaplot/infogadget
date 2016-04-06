<?php

namespace app\components;

use yii\base\Component;
use app\models\Brand;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class SourceParser extends Component
{

    public $brand_id = 1;
    public $base_uri = 'http://shop.lenovo.com';
    
    public $map = [
        
    ];
    
    protected $models = [
        'brand' => false,
    ];

    public function init()
    {
        $this->models['brand'] = Brand::find()
                ->andWhere('id = :id', ['id' => $this->brand_id])
                ->one();
        return parent::init();
    }
    
    public function getModel($model) 
    {
        return $this->models[$model];
    }
    
    public function getProducts()
    {
        $client = new Client([
            'base_uri' => $this->base_uri
        ]);

       // $proxy = \app\models\Proxy::find()->orderBy('rand()')->one();

        
        $result = $client->request('GET', '/ru/ru/smartphones/?menu-id=Смартфоны'/**, [
            'proxy' => [
                'http' => $proxy->string,
            ]
        ]**/);
        
        
        $html = $result->getBody()->getContents();
        //var_dump($html);
        
        $crawler = new Crawler($html);
        
        $smartphones = $crawler->filter('li.subSeriesLink')->each(function ($node, $i) {
            $uri = $node->filter('a')->attr('href');
            return [
                'uri' => $uri,
                'title' => $node->filter('a')->text(),
                'info' => $this->getProductInfo($uri),
            ];
        });
        
        return $smartphones;
    }
    
    public function getProductInfo($uri)
    {
        $client = new Client([
            'base_uri' => $this->base_uri
        ]);

        //$proxy = \app\models\Proxy::find()->orderBy('rand()')->one();

        
        $result = $client->request('GET', $uri/**, [
            'proxy' => [
                'http' => $proxy->string,
            ]
        ]**/);
        
        
        $html = $result->getBody()->getContents();

        $crawler = new Crawler($html);
        $table = $crawler->filter('table.techSpecs-table > tbody > tr');
        if (empty($table))
            return [];

        $info = $table->each(function ($node, $i) {

            if (!$node->filter('td')->count()) {
                return false;
            }
            
            return [
                'title' => $node->filter('td')->eq(0)->html(),
                'value' => $node->filter('td')->eq(1)->html(),
            ];
        });
        
        return array_filter($info, 'is_array');
    }

}
