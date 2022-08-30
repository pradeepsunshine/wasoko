<?php
declare(strict_types=1);

namespace AC\MassOrderAction\Controller\Adminhtml\Invoice;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use AC\MassOrderAction\Model\MassOrderProcess;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;

class Process extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var MassOrderProcess
     */
    protected $massOrderProcess;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @param PageFactory $resultPageFactory
     * @param MassOrderProcess $massOrderProcess
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        PageFactory $resultPageFactory,
        MassOrderProcess $massOrderProcess,
        Context $context,
        Filter $filter
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->massOrderProcess = $massOrderProcess;
        $this->filter = $filter;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $error = false;
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        print_R($collection->getData());die;
        $params = $this->getRequest()->getParams();
        if (isset($params['selected']) && count($params['selected'])) {
            $error = $this->massOrderProcess->processInvoice($params['selected']);
        } else {
            $this->messageManager->addErrorMessage('Please select atleast one order.');
        }

        if ($error) {
            $this->messageManager->addErrorMessage($error);
        } else {
            $this->messageManager->addSuccessMessage('Invoice generated successfully.');
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}

