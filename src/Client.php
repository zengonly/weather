<?php


namespace Zengonly\Weather;


use Zengonly\Weather\Exceptions\Exception;
use Zengonly\Weather\Exceptions\HttpException;
use Zengonly\Weather\Exceptions\InvalidArgumentException;

class Client
{
    public static function run()
    {
        try {
            $weather = new Weather('789ce537319b123e25ae1aa49543693f');
            $res = $weather->getWeather('济南');
            var_dump($res);
        } catch (Exception $e) {
            $message = $e->getMessage();

            if ($e instanceof InvalidArgumentException) {
                $message = '参数异常：' . $message;
            } elseif ($e instanceof HttpException) {
                $message = '接口异常：' . $message;
            }

            echo '调用天气扩展时出现了异常：' . $message;
        }
    }
}