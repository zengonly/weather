<?php


namespace Zengonly\Weather;


use GuzzleHttp\Client;
use Zengonly\Weather\Exceptions\HttpException;
use Zengonly\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    /**
     * 高德开放平台创建的应用 API Key
     *
     * @var string
     */
    protected $key;

    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * 获取天气
     *
     * @param string $city
     * @param string $type
     * @param string $format
     * @return mixed|string
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function getWeather(string $city, $type = 'base', $format = 'json')
    {
        $url = "https://restapi.amap.com/v344/weather/weatherInfo";

        // 1. 对 `$format` 与 `$type` 参数进行检查，不在范围内的抛出异常。
        if (!in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        // 2. 封装 query 参数，并对空值进行过滤
        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type
        ]);

        try {
            // 3. 调用 getHttpClient 获取实例，并调用该实例的 `get` 方法，
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            // 4. 返回值根据 $format 返回不同的格式，
            // 当 $format 为 json 时，返回数组格式，否则为 xml。
            return 'json' == $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            // 5. 当调用出现异常时捕获并抛出，消息为捕获到的异常消息，
            // 并将调用异常作为 $previousException 传入。
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }
}