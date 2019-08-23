<?php

namespace Vmasciotta\PrintOrderPdf\Sales\Order\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintAction extends \Magento\Sales\Controller\Order\PrintAction
{
    protected $registry;
    protected $orderPdfFactory;
    protected $date;
    protected $fileFactory;

    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Fooman\PrintOrderPdf\Model\Pdf\OrderFactory $orderPdfFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        /** @var \Magento\Framework\Registry _registy */
        $this->registry = $registry;
        $this->orderPdfFactory = $orderPdfFactory;
        $this->date = $date;
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $orderLoader, $resultPageFactory);
    }

    public function execute()
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result) {
            $order = $this->registry->registry('current_order');
            if ($order->getEntityId()) {
                $pdf = $this->orderPdfFactory->create()->getPdf([$order]);
                $date = $this->date->date('Y-m-d_H-i-s');
                return $this->fileFactory->create(
                    __('order') . '_' . $date . '.pdf',
                    $pdf->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        }
    }
}