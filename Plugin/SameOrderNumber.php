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

use Magento\SalesSequence\Model\Sequence;
use Magento\Framework\App\Request\Http;
use Magento\Sales\Model\Order;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

use Mageplaza\SameOrderNumber\Helper\Data as HelperData;
use Mageplaza\SameOrderNumber\Model\System\Config\Source\Apply;

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
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * SameOrderNumber constructor.
     * @param Http $request
     * @param Order $order
     * @param HelperData $helperData
     * @param State $state
     */
    public function __construct(Http $request,
                                Order $order,
                                HelperData $helperData,
                                State $state)
    {
        $this->_request = $request;
        $this->_order = $order;
        $this->_helperData = $helperData;
        $this->_state = $state;
    }


    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isBackend()
    {
        return $this->_state->getAreaCode() === Area::AREA_ADMINHTML;
    }

    /**
     * Get the current order
     * @return Order
     */
    public function getOrder()
    {
        $orderId = $this->_request->getParam('order_id');
        /** @var \Magento\Sales\Model\Order $order */
        $order   = $this->_order->load($orderId);
        return $order;
    }

    /**
     * Process next counter
     * @param $defaultIncrementId
     * @param $type
     * @return string
     */
    public function processIncrementId($defaultIncrementId, $type) {
        if($type != null) {
            switch ($type) {
                case Apply::INVOICE:
                    $invoiceCollection = $this->getOrder()->getInvoiceCollection();
                    $orderIncrementId = $this->getOrder()->getIncrementId();
                    $nextInvoiceId = count($invoiceCollection->getAllIds()) + 1;
                    $newIncrementId = $orderIncrementId . "-" .$nextInvoiceId;
                    return $newIncrementId;
                    break;
                case Apply::SHIPMENT:
                    $shipmentCollection = $this->getOrder()->getShipmentsCollection();
                    $orderIncrementId = $this->getOrder()->getIncrementId();
                    $nextShipmentId = count($shipmentCollection->getAllIds()) + 1;
                    $newIncrementId = $orderIncrementId . "-" .$nextShipmentId;
                    return $newIncrementId;
                    break;
                case Apply::CREDIT_MEMO:
                    $creditMemoCollection = $this->getOrder()->getCreditmemosCollection();
                    $orderIncrementId = $this->getOrder()->getIncrementId();
                    $nextCreditMemoId = count($creditMemoCollection->getAllIds()) + 1;
                    $newIncrementId = $orderIncrementId . "-" .$nextCreditMemoId;
                    return $newIncrementId;
                    break;
            }
        }
        return $defaultIncrementId;
    }

    /**
     * @param Sequence $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetCurrentValue(Sequence $subject, \Closure $proceed)
    {
        $defaultIncrementId = $proceed();
        if($this->isBackend()) {
            $type = null;
            $storeId = $this->getOrder()->getStore()->getId();
            if($this->_helperData->isEnabled($storeId)) {
                if($this->_request->getPost('invoice') && $this->_helperData->isApplyInvoice($storeId)) {
                    $type = Apply::INVOICE;
                }
                if($this->_request->getPost('shipment') && $this->_helperData->isApplyShipment($storeId)) {
                    $type = Apply::SHIPMENT;
                }
                if($this->_request->getPost('creditmemo') && $this->_helperData->isApplyCreditMemo($storeId)) {
                    $type = Apply::CREDIT_MEMO;
                }
                return $this->processIncrementId($defaultIncrementId, $type);
            }
            return $defaultIncrementId;
        }
        return $defaultIncrementId;
    }
}