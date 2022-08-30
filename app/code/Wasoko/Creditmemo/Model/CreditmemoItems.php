<?php
declare(strict_types=1);

namespace Wasoko\Creditmemo\Model;

use Magento\Framework\Model\AbstractModel;

class CreditmemoItems extends AbstractModel
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Wasoko\Creditmemo\Model\ResourceModel\CreditmemoItems::class);
    }
}

