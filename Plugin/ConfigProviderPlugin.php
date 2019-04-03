<?php
/***
 * Copyright © Sqli LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.Sqli.com | support@Sqli.com
 */

namespace Sqli\OrderDetails\Plugin;


class ConfigProviderPlugin extends \Magento\Framework\Model\AbstractModel
{
    protected $taxCalculation;
    protected $itemFactory;
    protected $productFactory;


    /**
     * ConfigProviderPlugin constructor.
     * @param \Magento\Tax\Model\TaxCalculationFactory $taxCalculation
     * @param \Magento\Quote\Model\Quote\ItemFactory $itemFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Tax\Model\TaxCalculationFactory $taxCalculation,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ){
        $this->taxCalculation=$taxCalculation;
        $this->itemFactory=$itemFactory;
        $this->productFactory=$productFactory;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $items = $result['totalsData']['items'];

        for($i=0;$i<count($items);$i++){
            $quote = $this->itemFactory->create()->load($items[$i]['item_id']);
            $product = $this->productFactory->create()->load($quote->getProductId());
            $items[$i]['tax_rate'] = $this->taxCalculation->create()->getCalculatedRate($product->getTaxClassId(), null, $product->getStoreId());
        }

        $result['totalsData']['items'] = $items;
        return $result;
    }

}