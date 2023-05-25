<?php
namespace ReesMcIvor\PickingList\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_orderFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_orderFactory = $orderFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $order = $this->_orderFactory->create()->loadByIncrementId('000000001'); // Replace with your order increment ID

        $items = $order->getAllVisibleItems();

        foreach ($items as $item) {
            echo 'Name: ' . $item->getName() . '<br />';
            echo 'Sku: ' . $item->getSku() . '<br />';
            echo 'Quantity: ' . $item->getQtyOrdered() . '<br />';
            echo 'Price: ' . $item->getPrice() . '<br />';
            echo '<br />';
        }

        exit;
    }
}