<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Magento\Framework\Model\AbstractModel;

class Invoice extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Wasoko\ZRAIntegration\Model\ResourceModel\Invoice::class);
    }
}

