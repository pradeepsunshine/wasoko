<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class CreateorderManagement
 * @package PradeepToptal\CreateOrderApi\Model
 */
class CreateorderManagement implements \PradeepToptal\CreateOrderApi\Api\CreateorderManagementInterface
{
    const GENERATED_FLAG = 'toptal_order_generated';

    const FREE_SHIPPING_METHOD_CODE = 'freeshipping_freeshipping';

    const CHECKMO_PAYMENT_METHOD_CODE = 'checkmo';

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepositoryInterface;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * @var CartManagementInterface
     */
    private $cartManagementInterface;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManagerInterface;

    /**
     * CreateorderManagement constructor.
     * @param StoreManagerInterface $storeManagerInterface
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param QuoteFactory $quoteFactory
     * @param AddressRepositoryInterface $addressRepositoryInterface
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param CartManagementInterface $cartManagementInterface
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        CustomerRepositoryInterface $customerRepositoryInterface,
        QuoteFactory $quoteFactory,
        AddressRepositoryInterface $addressRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface,
        CartManagementInterface $cartManagementInterface,
        Session $customerSession,
        CustomerFactory $customerFactory,
        ManagerInterface $messageManager
    )
    {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteFactory = $quoteFactory;
        $this->addressRepositoryInterface = $addressRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * POST for createorder api
     * @param string $param
     * @return string
     */
    public function postCreateorder($param)
    {
        if(!$this->isAuthorized()) {
            throw new \Magento\Framework\Webapi\Exception(__('This action is not authorized.'));
        }
        $paramsArray = $this->decodeParam($param);
        if (!count($paramsArray) ||
            !isset($paramsArray['prod']['id']) ||
            !isset($paramsArray['prod']['qty']) ||
            !isset($paramsArray['customer'])
            ) {
            throw new \Magento\Framework\Webapi\Exception(__('Please select products and customer.'));
        }
        $this->processOrder($paramsArray);
    }

    /**
     * @return mixed
     */
    private function isAuthorized()
    {
        return $this->customerSession->getIsCreateOrderAuthorized();
    }

    /**
     * @param $param
     * @return array
     */
    private function decodeParam($param)
    {
        $query = [];
        $parts = parse_url($param);
        if (isset($parts['path'])) {
            parse_str($parts['path'], $query);
        }
        return $query;
    }

    /**
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function processOrder($paramsArray)
    {
        $customerId = $paramsArray['customer'];
        $this->customer = $this->getCustomer($customerId);
        if ($this->customer && $this->customer->getId()) {
            $this->quote = $this->createQuoteAssignCustomer($this->customer);
            // To avoid error "Invalid state change requested".
            $this->setCustomerAsLoggedIn();
            $this->addProducts($paramsArray);
            $this->addBillingAndShippingAddress();
            $this->collectQuoteTotalsSavePayment();
            if ($this->quote->getGrandTotal() < 1000) {
                throw new \Magento\Framework\Webapi\Exception(__('Order total can not be less than 1000.'));
            }
            $this->placeOrder();
        }
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomer($customerId)
    {
        return $this->customerRepositoryInterface->getById($customerId);
    }

    /**
     * @return Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createQuoteAssignCustomer()
    {
        $this->store = $this->storeManagerInterface->getStore();

        /** @var Quote $newQuote */
        $newQuote = $this->quoteFactory->create();
        $newQuote->setData(self::GENERATED_FLAG, true);
        $newQuote->setIsActive(true);
        $newQuote->setStore($this->store);
        $newQuote->setBaseCurrencyCode($this->store->getBaseCurrencyCode());
        $newQuote->setGlobalCurrencyCode($this->store->getBaseCurrencyCode());
        $newQuote->setQuoteCurrencyCode($this->store->getBaseCurrencyCode());
        $newQuote->setStoreCurrencyCode($this->store->getBaseCurrencyCode());
        $newQuote->setCustomerFirstname($this->customer->getFirstname());
        $newQuote->setCustomerLastname($this->customer->getLastname());
        $newQuote->setCustomerEmail($this->customer->getEmail());
        $newQuote->assignCustomer($this->customer);
        $newQuote->setCustomerIsGuest(false);
        $newQuote->setStoreId($this->store->getId());
        return $newQuote;
    }

    /**
     * Login customer explicitly to avoid Invalid state change error.
     */
    private function setCustomerAsLoggedIn()
    {
        $customer = $this->customerFactory->create()->load($this->customer->getId());
        $this->customerSession->setCustomerAsLoggedIn($customer);
    }

    /**
     * @param $paramsArray
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addProducts($paramsArray)
    {
        foreach($paramsArray['prod']['id'] as $productId) {

            $product = $this->productRepositoryInterface->getById(
                $productId,
                false,
                $this->store->getId(),
                true
            );
            $this->quote->addProduct($product, $paramsArray['prod']['qty'][(int)$productId]);
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addBillingAndShippingAddress()
    {
        $billingAddressId = $this->customer->getDefaultBilling();
        $billingAddressDetails = $this->quote->getBillingAddress();
        if($billingAddressId) {
            $billingAddress = $this->addressRepositoryInterface->getById($billingAddressId);
            $billingAddressDetails->setEmail($this->customer->getEmail());
            $billingAddressDetails->setFirstname($billingAddress->getFirstname());
            $billingAddressDetails->setLastname($billingAddress->getLastname());
            $billingAddressDetails->setStreet($billingAddress->getStreet());
            $billingAddressDetails->setCity($billingAddress->getCity());
            $billingAddressDetails->setRegionId($billingAddress->getRegionId());
            $billingAddressDetails->setRegion($billingAddress->getRegion());
            $billingAddressDetails->setPostcode($billingAddress->getPostcode());
            $billingAddressDetails->setTelephone($billingAddress->getTelephone());
            $billingAddressDetails->setCountryId($billingAddress->getCountryId());
            $this->quote->setBillingAddress($billingAddressDetails);
        } else {
            $dummyAddress = $this->getDummyAddressArray();
            $billingAddressDetails->addData($dummyAddress);
        }

        $shippingAddressId = $this->customer->getDefaultShipping();
        $shippingAddressDetails = $this->quote->getShippingAddress();
        if($shippingAddressId) {
            $shippingAddress = $this->addressRepositoryInterface->getById($shippingAddressId);
            $shippingAddressDetails->setEmail($this->customer->getEmail());
            $shippingAddressDetails->setFirstname($shippingAddress->getFirstname());
            $shippingAddressDetails->setLastname($shippingAddress->getLastname());
            $shippingAddressDetails->setStreet($shippingAddress->getStreet());
            $shippingAddressDetails->setCity($shippingAddress->getCity());
            $shippingAddressDetails->setRegionId($shippingAddress->getRegionId());
            $shippingAddressDetails->setRegion($shippingAddress->getRegion());
            $shippingAddressDetails->setPostcode($shippingAddress->getPostcode());
            $shippingAddressDetails->setTelephone($shippingAddress->getTelephone());
            $shippingAddressDetails->setCountryId($shippingAddress->getCountryId());
        } else {
            $dummyAddress = $this->getDummyAddressArray();
            $shippingAddressDetails->addData($dummyAddress);
        }
        $shippingAddressDetails->setCollectShippingRates(true);
        $shippingAddressDetails->setShippingMethod(self::FREE_SHIPPING_METHOD_CODE);

        $this->quote->setShippingAddress($shippingAddressDetails);
    }

    /**
     * @return array
     */
    private function getDummyAddressArray()
    {
        $address = [
            'email' => $this->customer->getEmail(),
            'firstname' => $this->customer->getFirstname(),
            'lastname' => $this->customer->getLastname(),
            'street' => '491 eve sun dr',
            'city' => 'Prosper',
            'region_id' => 57,
            'region' => 'Texas',
            'postcode' => '75078',
            'telephone' => '9999999999',
            'country_id' => 'US'
        ];
        return $address;
    }


    /**
     * @return Void
     * @throws \Exception
     */
    private function collectQuoteTotalsSavePayment()
    {
        $this->quote->setTotalsCollectedFlag(false);
        $payment = $this->quote->getPayment();
        $payment->setMethod(self::CHECKMO_PAYMENT_METHOD_CODE);
        $this->quote->setPayment($payment);
        $this->quote->setInventoryProcessed(false);
        $this->quote->collectTotals()->save();

    }

    /**
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function placeOrder()
    {
        $order = $this->cartManagementInterface->submit($this->quote);
        $this->customerSession->logout();
        //Keep the flag true even if customer is logged out.
        $this->customerSession->start()->setIsCreateOrderAuthorized(true);
        return $order;

    }
}

