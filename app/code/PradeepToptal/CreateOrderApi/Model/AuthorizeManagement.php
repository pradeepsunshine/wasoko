<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

/**
 * Class AuthorizeManagement
 * @package PradeepToptal\CreateOrderApi\Model
 */
class AuthorizeManagement
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    const GET_TOKEN_URL = '/rest/V1/integration/admin/token';

    /**
     * AuthorizeManagement constructor.
     * @param Curl $curl
     * @param SerializerInterface $json
     * @param StoreManagerInterface $storeManagerInterface
     * @param Session $customerSession
     */
    public function __construct(
        Curl $curl,
        SerializerInterface $json,
        StoreManagerInterface $storeManagerInterface,
        Session $customerSession
    )
    {
        $this->curl = $curl;
        $this->json = $json;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerSession = $customerSession;
    }

    /**
     * POST for authorize api
     * @param string $user
     * @param string $password
     * @return string
     */
    public function authorizeAdminUser($user, $password)
    {
       $token = $this->getAuhorizationToken($user, $password);
       if(is_array($token)) {
           throw new \Magento\Framework\Webapi\Exception(__($token['message']));
       }
       $this->customerSession->setIsCreateOrderAuthorized(true);
    }

    private function getAuhorizationToken($user, $password)
    {
        $requstbody = ["username" => $user, "password" => $password];
        $baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl();
        $url = $baseUrl.self::GET_TOKEN_URL;
        $this->curl->setOption(CURLOPT_HEADER, 0);
        $this->curl->setOption(CURLOPT_TIMEOUT, 60);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->post($url, $this->json->serialize($requstbody));
        $response = $this->curl->getBody();
        $responseBody = $this->json->unserialize($response);
        return $responseBody;
    }
}
