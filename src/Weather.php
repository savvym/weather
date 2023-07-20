<?php

namespace Savvym\Weather;

use GuzzleHttp\Client;
use Savvym\Weather\Exceptions\InvalidArgumentException;
use Savvym\Weather\Exceptions\HttpException;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];


    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function setGuuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function getWeather(string $city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type,
        ]);
        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();
            return $format === 'json' ? json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
