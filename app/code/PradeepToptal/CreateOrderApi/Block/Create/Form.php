<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Block\Create;

/**
 * Class Form
 * @package PradeepToptal\CreateOrderApi\Block\Create
 */
class Form extends \Magento\Framework\View\Element\Template
{
    const REST_PREFIX = 'rest/V1';

    const GET_PRODUCT_LIST_ENDPOINT = '/pradeeptoptal-createorderapi/listallprods/';

    const GET_CUSTOMER_LIST_ENDPOINT = '/pradeeptoptal-createorderapi/listallcustomers/';

    const CREATE_ORDER_ENDPOINT = '/pradeeptoptal-createorderapi/createorder/';

    const AUTH_USER_ENDPOINT = '/pradeeptoptal-createorderapi/authorize/';

    /**
     * @var \Magento\Customer\Model\Session;
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    private $baseUrl;

    /**
     * Form constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param array $data
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context, $data);
        $this->baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl();

    }

    /**
     * @return string
     */
    public function getListProductsUrl()
    {
        return $this->baseUrl . self::REST_PREFIX . self::GET_PRODUCT_LIST_ENDPOINT;
    }

    /**
     * @return string
     */
    public function getListCustomersUrl()
    {
        return $this->baseUrl . self::REST_PREFIX . self::GET_CUSTOMER_LIST_ENDPOINT;
    }

    /**
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->baseUrl . self::REST_PREFIX . self::CREATE_ORDER_ENDPOINT;
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->baseUrl . self::REST_PREFIX . self::AUTH_USER_ENDPOINT;
    }

    /**
     * @return mixed
     */
    public function isAuthorized()
    {
        return $this->customerSession->getIsCreateOrderAuthorized();
    }
}

