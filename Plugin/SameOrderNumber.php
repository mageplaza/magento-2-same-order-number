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

namespace Mageplaza\SameOrderNumber\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\SalesSequence\Model\Sequence;
use Mageplaza\SameOrderNumber\Helper\Data as HelperData;
use Mageplaza\SameOrderNumber\Model\System\Config\Source\Apply;

/**
 * Class SameOrderNumber
 * @package Mageplaza\SameOrderNumber\Plugin
 */
class SameOrderNumber
{
    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * SameOrderNumber constructor.
     *
     * @param Http $request
     * @param Order $order
     * @param HelperData $helperData
     * @param Registry $registry
     */
    public function __construct(
        Http $request,
        Order $order,
        HelperData $helperData,
        Registry $registry
    )
    {
        $this->_request    = $request;
        $this->_order      = $order;
        $this->_helperData = $helperData;
        $this->_registry   = $registry;
    }

    /**
     * Get the current order
     * @return Order
     */
    public function getOrder()
    {
        $orderId = $this->_request->getParam('order_id');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_order->load($orderId);

        return $order;
    }

    /**
     * @param $collection
     *
     * @return string
     */
    public function getNextId($collection)
    {
        $currentIncrementId = $this->getOrder()->getIncrementId();
        $newIncrementId     = $currentIncrementId;
        if (count($collection) > 0) {
            $totalIds       = count($collection);
            $newIncrementId = $currentIncrementId . "-" . $totalIds;
        }

        return $newIncrementId;
    }

    /**
     * Process next counter
     *
     * @param $defaultIncrementId
     * @param $type
     * @param Invoice|null $invoice
     *
     * @return string
     */
    public function processIncrementId($defaultIncrementId, $type, Invoice $invoice = null)
    {
        if ($type != null) {
            switch ($type) {
                case Apply::INVOICE:
                    if ($invoice != null) {
                        return $invoice->getOrder()->getIncrementId();
                    }

                    $invoiceCollectionIds = $this->getOrder()->getInvoiceCollection()->getAllIds();

                    return $this->getNextId($invoiceCollectionIds);

                case Apply::SHIPMENT:
                    $shipmentCollectionIds = $this->getOrder()->getShipmentsCollection()->getAllIds();

                    return $this->getNextId($shipmentCollectionIds);

                case Apply::CREDIT_MEMO:
                    $creditMemoCollectionIds = $this->getOrder()->getCreditmemosCollection()->getAllIds();

                    return $this->getNextId($creditMemoCollectionIds);
            }
        }

        return $defaultIncrementId;
    }

    /**
     * @param Sequence $subject
     * @param \Closure $proceed
     *
     * @return mixed|string
     */
    public function aroundGetCurrentValue(Sequence $subject, \Closure $proceed)
    {
        $defaultIncrementId = $proceed();
        $type               = null;
        $storeId            = $this->getOrder()->getStore()->getId();
        if ($this->_helperData->isAdmin() && $this->_helperData->isEnabled($storeId)) {
            if ($this->_request->getPost('invoice') && $this->_helperData->isApplyInvoice($storeId)) {
                $type = Apply::INVOICE;
            }
            if ($this->_request->getPost('shipment') && $this->_helperData->isApplyShipment($storeId)) {
                $type = Apply::SHIPMENT;
            }
            if ($this->_request->getPost('creditmemo') && $this->_helperData->isApplyCreditMemo($storeId)) {
                $type = Apply::CREDIT_MEMO;
            }

            return $this->processIncrementId($defaultIncrementId, $type);
        }
        /**
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         */
        if ($invoice = $this->_registry->registry('son_new_invoice')) {
            return $this->processIncrementId($defaultIncrementId, Apply::INVOICE, $invoice);
        }

        return $defaultIncrementId;
    }
}