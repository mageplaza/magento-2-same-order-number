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

namespace Mageplaza\SameOrderNumber\Helper;

use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\SameOrderNumber\Model\System\Config\Source\Apply;

/**
 * Class Data
 * @package Mageplaza\SameOrderNumber\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'sameordernumber';

    public function getApplyForOption($storeId = null) {
        return explode(",", $this->getConfigGeneral('apply', $storeId));
    }

    public function isApplyInvoice($storeId = null) {
        if (in_array(Apply::SHIPMENT, $this->getApplyForOption($storeId))) {
            return true;
        }
        return false;
    }

    public function isApplyShipment($storeId = null) {
        if (in_array(Apply::INVOICE, $this->getApplyForOption($storeId))) {
            return true;
        }
        return false;
    }

    public function isApplyCreditMemo($storeId = null) {
        if (in_array(Apply::CREDIT_MEMO, $this->getApplyForOption($storeId))) {
            return true;
        }
        return false;
    }

}