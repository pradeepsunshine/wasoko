<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model\Http;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Client
 * @package PradeepToptal\WeatherReport\Model\Http
 */
class Client
{
    const API_HOST_CONFIG_PATH = 'weatherapi/general/url';
    const API_KEY_CONFIG_PATH = 'weatherapi/general/key';
    const QUERY_STRING_PREFIX = '?q=';
    const QUERY_STRING_SEPARATOR = '&';
    const QUERY_STRING_APP_ID = 'appid';
    const QUERY_STRING_UNITS = 'units';
    const QUERY_STRING_UNIT_METRIC = 'metric';
    const SUCCESS_RESPONSE_CODE = 200;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Client constructor.
     * @param Curl $curl
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Curl $curl,
        ScopeConfigInterface $scopeConfig

    )
    {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $city
     * @param $country
     * @return false|string
     */
    public function getWeatherDataForCity($city, $country)
    {
        $url = $this->createApiUrl($city, $country);
        if ($url) {
            $this->curl->get($url);
            $response = $this->curl->getBody();
            if ($this->curl->getStatus() == self::SUCCESS_RESPONSE_CODE) {
                return $response;
            }
        }
        return false;
    }

    /**
     * @param $city
     * @return false|string
     */
    private function createApiUrl($city, $country)
    {
        $apiHost = $this->scopeConfig->getValue(self::API_HOST_CONFIG_PATH);
        $apiKey = $this->scopeConfig->getValue(self::API_KEY_CONFIG_PATH);
        $city = urlencode($city);
        if($apiHost && $apiKey) {
            return $apiHost .
                self::QUERY_STRING_PREFIX . $city . ',' . $country .
                self::QUERY_STRING_SEPARATOR . self::QUERY_STRING_UNITS . '=' . self::QUERY_STRING_UNIT_METRIC .
                self::QUERY_STRING_SEPARATOR . self::QUERY_STRING_APP_ID . '=' . $apiKey;
        }
        return false;
    }

}
