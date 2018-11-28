<?php
/**
 * Created by PhpStorm.
 * User: Dinh Phuc Tran
 * Date: 28-Nov-18
 * Time: 09:56
 */

namespace Mageplaza\SameOrderNumber\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class InvoiceSaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(Registry $registry)
    {
        $this->_registry = $registry;
    }
    
    /**
     * @param Observer $observer
     * @return InvoiceSaveBefore
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         */
        $invoice = $observer->getData('invoice');
        if(!$invoice) {
            return $this;
        }
        if($invoice->isObjectNew()) {
            $this->_registry->register('son_new_invoice', $invoice);
        }
        return $this;
    }
}