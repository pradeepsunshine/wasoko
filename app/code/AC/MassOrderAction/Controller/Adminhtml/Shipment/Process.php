<?php
declare(strict_types=1);

namespace AC\MassOrderAction\Controller\Adminhtml\Shipment;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use AC\MassOrderAction\Model\MassOrderProcess;
use Magento\Backend\App\Action\Context;

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
     * @param PageFactory $resultPageFactory
     * @param MassOrderProcess $massOrderProcess
     */
    public function __construct(
        PageFactory $resultPageFactory,
        MassOrderProcess $massOrderProcess,
        Context $context
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->massOrderProcess = $massOrderProcess;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $error = false;

        $params = $this->getRequest()->getParams();
        if (isset($params['selected']) && count($params['selected'])) {
            $error = $this->massOrderProcess->processShipments($params['selected']);
        } else {
            $this->messageManager->addErrorMessage('Please select atleast one order.');
        }

        if ($error) {
            $this->messageManager->addErrorMessage($error);
        } else {
            $this->messageManager->addSuccessMessage('Shipment(s) generated successfully.');
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
