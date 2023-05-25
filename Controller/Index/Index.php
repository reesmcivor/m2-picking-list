<?php

namespace ReesMcIvor\PickingList\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_orderCollectionFactory;
    protected $_jsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_jsonFactory = $jsonFactory;
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
                    'Price' => $item->getPrice()
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
}
