<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

$state = $obj->get(Magento\Framework\App\State::class);
$state->setAreaCode('adminhtml');

$object = $obj->create(\Wasoko\ZRAIntegration\Model\SyncInvoice::class);
//$object = $obj->create(\Wasoko\ZRAIntegration\Model\SyncMemo::class);
$object->sync();
