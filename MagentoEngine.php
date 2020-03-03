<?php

    require 'app/bootstrap.php';
    use Magento\Framework\App\Bootstrap;

    class Magento_Engine
    {
        private $bootstrap;
        private $objectManager;

        function Magento_Engine()
        {
            $this->bootstrap = Bootstrap::create(BP, $_SERVER);
            $this->objectManager = $this->bootstrap->getObjectManager();
        }

        /**
         * @param $id
         * @param $name
         * @param string $model
         * @return bool
         */
        function update_product($id, $name,$model = '\Magento\Catalog\Model\ProductFactory')
        {
            try
            {
                $state = $this->objectManager->get('\Magento\Framework\App\State');
                $state->setAreaCode('frontend');
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productFactory = $objectManager->create($model);
                $product = $productFactory->create();
                $product->load($id);
                for($i = 0; $i <= $this->getStoreID(); $i++)
                {
                    if(!empty($product->getData('sku')))
                    {
                        $product
                            ->setStoreId($i)
                            ->setName($name)
                            ->save();
                    }
                }
                return true;
            }catch (Exception $ex)
            {
                return false;
            }
        }
        /**
         * @param string $model
         * @param $id
         * @return Single Param
         * Example return_row("Magento\Catalog\Model\Product","1")->getsku();
         * Syntax  return_row(Model,ID)->get[TAB NAME];
         */
        public function return_row($model = 'Magento\Catalog\Model\Product', $id)
        {
            $state = $this->objectManager->get('\Magento\Framework\App\State');
            $state->setAreaCode('frontend');
            $prodotto = $this->objectManager->create($model);
            $prodotto->load($id);
            return $prodotto;
        }

        /**
         * @param $model
         * @return mixed
         * Using $db->return_all_row()->getLastPageNumber(); return page number
         * Usage: foreach (return_all_row as $product) echo $product->get[TAB NAME];
         */
        public function return_all_row($row,$queryName,$model = 'Magento\Catalog\Model\ResourceModel\Product\Collection')
        {
            $state = $this->objectManager->get('\Magento\Framework\App\State');
            $state->setAreaCode('frontend');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productCollection = $objectManager->create($model);
            $collection = $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter(
                    [
                        ['attribute' => $row, 'like' => '%'.$queryName.'%']
                    ])
                ->setPageSize(10)
                ->load();

            return $collection;

        }


        public function getStoreID()
        {
            $url = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
            $storeManager = $storeManager->getStore();
            $storeId = $storeManager->getStoreId();
            return $storeId;
        }
    }