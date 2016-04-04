<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Proxy;

class ProxyController extends Controller
{

    public function actionLoad()
    {
        $proxyPath = \yii::$app->basePath . "/proxy";

        try {
            $handle = opendir($proxyPath);
        } catch (Exception $e) {
            throw new \ErrorException('Не удалось открыть папку "' . $proxyPath . '".');
        }

        while (false !== ($dir = readdir($handle))) {
            if (!preg_match("/^proxylist\-\d{2}\-\d{2}\-\d{2}$/", $dir)) {
                continue;
            }

            $proxyFile = "{$proxyPath}/{$dir}/full_list_nopl/_reliable_list.txt";

            if (!file_exists($proxyFile)) {
                throw new \ErrorException('Папка "' . $dir . '" не содержит список proxy.');
            }

            $proxyList = file($proxyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $proxyList = array_filter($proxyList, function($proxyLine) {
                if (!preg_match("/^(?P<address>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(?P<port>\d{2,6})/", $proxyLine, $matches)) {
                    return false;
                }

                if (!filter_var($matches['address'], FILTER_VALIDATE_IP)) {
                    return false;
                }

                return true;
            });

            $proxyList = array_map(function($proxyLine) {
                preg_match("/^(?P<address>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(?P<port>\d{2,6})/", $proxyLine, $matches);
                return ['address' => ip2long($matches['address']), 'port' => $matches['port']];
            }, $proxyList);

            $successCount = Proxy::import($proxyList);

            echo date("Y-m-d H:i:s") . " Из папки {$dir} добавлено {$successCount} адресов" . PHP_EOL;
        }
        closedir($handle);
    }

}
