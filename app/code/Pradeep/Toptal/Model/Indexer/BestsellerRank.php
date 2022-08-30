<?php
declare(strict_types=1);

namespace Pradeep\Toptal\Model\Indexer;


class BestsellerRank implements \Magento\Framework\Indexer\ActionInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }
    /*
         * Used by mview, allows process indexer in the "Update on schedule" mode
         */
    public function execute($ids){
        //Used by mview, allows you to process multiple placed orders in the "Update on schedule" mode
    }

    /*
     * Will take all of the data and reindex
     * Will run when reindex via command line
     */
    public function executeFull(){
        $productCollection = $this->collectionFactory->create();
        foreach($productCollection as $product) {
            $product->setBestsellerRank(rand(1,100000));
            $product->getResource()->saveAttribute($product, 'bestseller_rank');
        }
    }

    /*
     * Works with a set of entity changed (may be massaction)
     */
    public function executeList(array $ids){
        //Works with a set of placed orders (mass actions and so on)
    }

    /*
     * Works in runtime for a single entity using plugins
     */
    public function executeRow($id){
        //Works in runtime for a single order using plugins
    }
}
