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

namespace Mageplaza\SameOrderNumber\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Apply
 * @package Mageplaza\SameOrderNumber\Model\System\Config\Source
 */
class Apply implements ArrayInterface
{
    const SHIPMENT = 'shipment';
    const INVOICE = 'invoice';
    const CREDIT_MEMO = 'creditmemo';

    /**
     * Return array of options as value-label pairs
     * Create option array for module configuration
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $applyOptions = [
            ['label' => __('Shipment'), 'value' => self::SHIPMENT],
            ['label' => __('Invoice'), 'value' => self::INVOICE],
            ['label' => __('Credit Memo'), 'value' => self::CREDIT_MEMO]
        ];

        return $applyOptions;
    }
}
