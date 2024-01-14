<?php

namespace ReesMcIvor\PickingList\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_orderCollectionFactory;
    protected $_jsonFactory;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_jsonFactory = $jsonFactory;
        $this->_productRepository = $productRepository;
        return parent::__construct($context);
    }

    public function execute()
    {
        $orderCollection = $this->_orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('status', 'processing');

        $ordersData = [];
        foreach ($orderCollection as $order) {
            $itemsData = [];
            $items = $order->getAllVisibleItems();

            foreach ($items as $item) {
                $itemsData[] = [
                    'Name' => $item->getName(),
                    'Sku' => $item->getSku(),
                    'Quantity' => $item->getQtyOrdered(),
                    'Price' => $item->getPrice(),
                    'Attributes' => $this->getProductAttributes($item)
                ];
            }
            $ordersData[] = [
                'OrderID' => $order->getIncrementId(),
                'Items' => $itemsData
            ];
        }

        $result = $this->_jsonFactory->create();
        $result->setData($ordersData);
        return $result;
    }

    /**
     * @param mixed $item
     * @return array
     */
    protected function getProductAttributes(mixed $item): array
    {
        $attributes = [];
        $product = null;
        try {
            $product = $this->_productRepository->get($item->getSku());
        } catch (\Exception $e) {
        }

        if ($product) {
            $productAttributes = $product->getAttributes();
            $attributesToMap = [
                'rm_u_classification',
                'rm_tray_type',
                'rm_clamp_type'
            ];

            foreach ($attributesToMap as $attribute) {
                if ($productAttributes[$attribute]) {
                    $attributes[$attribute] = $productAttributes[$attribute]
                        ->getFrontend()->getValue($product);
                }
            }
        }
        return $attributes;
    }
}
