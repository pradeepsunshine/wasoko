<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Wasoko\ZRAIntegration\Helper\Config;

class EsdZRAService
{
    const PROTOCOL = "https://";
    const CONFIG_API_URI = 'wasoko_zra/api/api_url';
    const CONFIG_API_USER = 'wasoko_zra/api/api_user';
    const CONFIG_API_KEY = 'wasoko_zra/api/api_key';

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param Config $configHelper
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        Config $configHelper
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @param $params
     * @param $requestMethod
     * @return \Magento\Framework\DataObject
     */
    public function execute($params, $requestMethod)
    {
        $fullEndPoint = $this->configHelper->getConfig(self::CONFIG_API_URI);
        $response = $this->doRequest($fullEndPoint, $params, $requestMethod);
        $status = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents();
        $responseDataObject = new \Magento\Framework\DataObject();
        if ($responseContent) {
            $responseDataObject->setStatus($status);
            $responseDataObject->setErrorReason($response->getReasonPhrase());
            $responseDataObject->setContent($responseContent);
            $responseDataObject->setRequestBody($params['body']);
        } else {
            $responseContent = ['No response from ESD.'];
            $responseDataObject->setStatus(400);
            $responseDataObject->setErrorReason(__('No response from ESD.'));
            $responseDataObject->setContent(json_encode($responseContent));
            $responseDataObject->setRequestBody($params['body']);
        }
        return $responseDataObject;
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {

        $params['headers'] = [
            "Content-Type" => "application/json"
        ];

        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::PROTOCOL . $this->configHelper->getConfig(self::CONFIG_API_URI)
            //'auth' => [$this->configHelper->getConfig(self::CONFIG_API_USER), $this->configHelper->getConfig(self::CONFIG_API_KEY)]
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                '',
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
