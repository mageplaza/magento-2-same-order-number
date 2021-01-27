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

use Closure;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Magento\SalesSequence\Model\ResourceModel\Meta;
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
     * @var Invoice
     */
    protected $_invoice;

    /**
     * @var Shipment
     */
    protected $_shipment;

    /**
     * @var Creditmemo
     */
    protected $_creditMemo;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Meta
     */
    protected $_meta;

    /**
     * @var AppResource as AppResource
     */
    protected $_connection;

    /**
     * SameOrderNumber constructor.
     *
     * @param Http $request
     * @param Order $order
     * @param Invoice $invoice
     * @param Shipment $shipment
     * @param Creditmemo $creditMemo
     * @param HelperData $helperData
     * @param Registry $registry
     * @param Meta $meta
     * @param AppResource $connection
     */
    public function __construct(
        Http $request,
        Order $order,
        Invoice $invoice,
        Shipment $shipment,
        Creditmemo $creditMemo,
        HelperData $helperData,
        Registry $registry,
        Meta $meta,
        AppResource $connection
    ) {
        $this->_request    = $request;
        $this->_order      = $order;
        $this->_invoice    = $invoice;
        $this->_shipment   = $shipment;
        $this->_creditMemo = $creditMemo;
        $this->_helperData = $helperData;
        $this->_registry   = $registry;
        $this->_meta       = $meta;
        $this->_connection = $connection->getConnection();
    }

    /**
     * Get the current order
     * @return Order
     */
    public function getOrder()
    {
        $orderId = $this->_request->getParam('order_id');
        /** @var Order $order */
        $order = $this->_order->load($orderId);

        return $order;
    }

    /**
     * @param $collection
     * @param $type
     *
     * @return string
     */
    public function getNextId($collection, $type)
    {
        $orderIncrementId = $this->getOrder()->getIncrementId();
        $newIncrementId   = $orderIncrementId;
        $totalIds         = count($collection);
        $firstId          = $this->isIncrementIdUnique($orderIncrementId, $type);

        if ($firstId && $totalIds <= 0) {
            $newIncrementId = $orderIncrementId . "-" . ($totalIds + 1);
        }

        if ($totalIds > 0) {
            $newIncrementId = $orderIncrementId . "-" . $totalIds;
            $nextId         = $this->isIncrementIdUnique($newIncrementId, $type);
            if ($nextId) {
                $newIncrementId = $orderIncrementId . "-" . ($totalIds + 1);
            }
        }

        return $newIncrementId;
    }

    /**
     * @param $incrementId
     * @param $type
     *
     * @return bool
     */
    public function isIncrementIdUnique($incrementId, $type)
    {
        $nextId     = null;
        $collection = null;
        switch ($type) {
            case Apply::INVOICE:
                $collection = $this->_invoice->getCollection();
                break;
            case Apply::SHIPMENT:
                $collection = $this->_shipment->getCollection();
                break;
            case Apply::CREDIT_MEMO:
                $collection = $this->_creditMemo->getCollection();
                break;
        }
        $nextId = $collection->addFieldToFilter('increment_id', $incrementId)->getLastItem()->getId();

        return $nextId !== null;
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
        if ($type !== null) {
            switch ($type) {
                case Apply::INVOICE:
                    if ($invoice !== null) {
                        $orderIncrementId = $invoice->getOrder()->getIncrementId();
                        $isNotUnique      = $this->isIncrementIdUnique($orderIncrementId, Apply::INVOICE);
                        if ($isNotUnique) {
                            $orderIncrementId = $invoice->getOrder()->getIncrementId() . "-1";
                        }

                        return $orderIncrementId;
                    }
                    $invoiceColIds = $this->getOrder()->getInvoiceCollection()->getAllIds();

                    return $this->getNextId($invoiceColIds, $type);
                case Apply::SHIPMENT:
                    $shipmentColIds = $this->getOrder()->getShipmentsCollection()->getAllIds();

                    return $this->getNextId($shipmentColIds, $type);
                case Apply::CREDIT_MEMO:
                    $creditMemoColIds = $this->getOrder()->getCreditmemosCollection()->getAllIds();

                    return $this->getNextId($creditMemoColIds, $type);
            }
        }

        return $defaultIncrementId;
    }

    /**
     * @param $storeId
     *
     * @return string|null
     */
    public function getType($storeId)
    {
        $type = null;

        if ($this->_request->getPost('invoice') && $this->_helperData->isApplyInvoice($storeId)) {
            $type        = Apply::INVOICE;
            $invoiceData = $this->_request->getPost('invoice');
            if (isset($invoiceData['do_shipment'])) {
                $type = Apply::SHIPMENT;
            }
        }
        if ($this->_request->getPost('invoice')) {
            $type        = Apply::INVOICE;
            $invoiceData = $this->_request->getPost('invoice');
            if (isset($invoiceData['do_shipment'])) {
                $type = Apply::SHIPMENT;
            }
        }
        if ($this->_request->getPost('shipment') && $this->_helperData->isApplyShipment($storeId)) {
            $type = Apply::SHIPMENT;
        }
        if ($this->_request->getPost('creditmemo') && $this->_helperData->isApplyCreditMemo($storeId)) {
            $type = Apply::CREDIT_MEMO;
        }

        return $type;
    }

    /**
     * @param $type
     * @param $storeId
     * @param $oderId
     *
     * @throws LocalizedException
     */
    public function jumpIncrementId($type, $storeId, $oderId)
    {
        if ($type !== null) {
            $sequenceTable  = $this->_meta->loadByEntityTypeAndStore($type, $storeId)->getSequenceTable();
            $idField        = $this->_connection->getAutoIncrementField($sequenceTable);
            $select         = $this->_connection->select()->from($sequenceTable)->order($idField . ' DESC');
            $curIncrementId = $this->_connection->fetchOne($select);
            if ((int) $oderId > (int) $curIncrementId) {
                $this->_connection->insert($sequenceTable, [$idField => (int) $oderId]);
            }
        }
    }

    /**
     * @param Sequence $subject
     * @param callable $proceed
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundGetNextValue(Sequence $subject, callable $proceed)
    {
        if ($this->_helperData->isAdmin()) {
            $storeId          = $this->getOrder()->getStore()->getId();
            $orderIncrementId = $this->getOrder()->getIncrementId();
            if ($storeId > 1) {
                $length           = strlen($orderIncrementId) - strlen($storeId);
                $orderIncrementId = substr($orderIncrementId, strlen($storeId), $length);
            }

            if ($this->_helperData->isEnabled($storeId)) {
                $type = $this->getType($storeId);
                $this->jumpIncrementId($type, $storeId, $orderIncrementId);
            }
        }

        return $proceed();
    }

    /**
     * @param Sequence $subject
     * @param Closure $proceed
     *
     * @return mixed|string
     * @SuppressWarnings(Unused)
     */
    public function aroundGetCurrentValue(Sequence $subject, Closure $proceed)
    {
        $defaultIncrementId = $proceed();
        if ($this->_helperData->isAdmin()) {
            $storeId = $this->getOrder()->getStore()->getId();
            if ($this->_helperData->isEnabled($storeId)) {
                $type = $this->getType($storeId);

                return $this->processIncrementId($defaultIncrementId, $type);
            }
        }

        if ($invoice = $this->_registry->registry('son_new_invoice')) {
            return $this->processIncrementId($defaultIncrementId, Apply::INVOICE, $invoice);
        }

        return $defaultIncrementId;
    }
}
