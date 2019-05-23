<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SameOrderNumber
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SameOrderNumber\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Mageplaza\SameOrderNumber\Helper\Data as HelperData;

/**
 * Class InvoiceSaveBefore
 * @package Mageplaza\SameOrderNumber\Observer
 */
class InvoiceSaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * InvoiceSaveBefore constructor.
     *
     * @param Registry $registry
     * @param HelperData $helperData
     */
    public function __construct(
        Registry $registry,
        HelperData $helperData)
    {
        $this->_registry   = $registry;
        $this->_helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @return InvoiceSaveBefore
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         */
        $invoice = $observer->getData('invoice');
        if (!$invoice) {
            return $this;
        }

        if($this->_helperData->isAdmin()) {
            return $this;
        }

        if($invoice->isObjectNew()) {
            $storeId = $invoice->getStore()->getId();
            if ($this->_helperData->isEnabled($storeId) && $this->_helperData->isApplyInvoice($storeId)) {
                $this->_registry->register('son_new_invoice', $invoice);
            }
        }

        return $this;
    }
}