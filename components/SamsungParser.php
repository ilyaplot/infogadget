<?php

namespace app\components;

use yii\base\Component;
use app\models\Brand;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class SamsungParser extends Component
{

    public $brand_id = 2;
    public $base_uri = 'http://www.samsung.com';
    
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

        
        $result = $client->request('GET', '/ru/consumer/mobile-devices/tablets/'/**, [
            'proxy' => [
                'http' => $proxy->string,
            ]
        ]**/);
        
        
        $html = $result->getBody()->getContents();
        //var_dump($html);
        
        $crawler = new Crawler($html);
        $smartphones = $crawler->filter('div.product-card.front')->each(function ($node, $i) {
            $uri = $node->filter('a')->eq(0)->attr('href');
            if (empty($uri))
                return false;
  
            return [
                'uri' => $uri,
                'title' => $this->filterTitle($node->filter('a')->eq(0)->attr('title')),
                'model' => preg_replace("/.*\|(.*)$/isu", "$1", $node->filter('a')->eq(0)->attr('data-omniture')),
                'info' => $this->getProductInfo($uri),
            ];
        });
        return array_filter($smartphones, 'is_array');
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
        $list = $crawler->filter('div.spec-list');
        
        if (!$list->count())
            return [];

        $info = $list->each(function ($node, $i) {
            
            return [
                'title' => $node->filter('h4')->html(),
                'value' => $node->filter('ul')->html(),
            ];
        });
        
        return array_filter($info, 'is_array');
    }
    
    public function filterTitle($title)
    {
        return preg_replace("/^Samsung\s/isu", "", $title);
    }

}
