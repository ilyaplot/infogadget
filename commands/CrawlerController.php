<?php

namespace app\commands;

use yii\console\Controller;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class CrawlerController extends Controller
{

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
    
    public function actionProxy()
    {
        $client = new Client([
            'base_uri' => 'http://2ip.ru/'
        ]);

        $proxy = \app\models\Proxy::find()->one();

        $result = $client->request('GET', '/', [
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

}
