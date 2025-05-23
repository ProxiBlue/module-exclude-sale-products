<?php
/**
 * @category  ProxiBlue
 * @package   ProxiBlue_ExcludeSaleProducts
 * @author    ProxiBlue
 * @copyright Copyright (c) 2023 ProxiBlue (https://www.proxiblue.com.au)
 */

namespace ProxiBlue\ExcludeSaleProducts\Plugin\SalesRule\Model;

use Magento\CatalogRule\Model\ResourceModel\Rule as CatalogRuleResourceModel;
use Magento\SalesRule\Model\Rule;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin for SalesRule\Model\Rule to exclude sale products from welcome discount
 */
class RulePlugin
{
    /**
     * @var CatalogRuleResourceModel
     */
    private $catalogRuleResourceModel;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CatalogRuleResourceModel $catalogRuleResourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CatalogRuleResourceModel $catalogRuleResourceModel,
        StoreManagerInterface $storeManager
    ) {
        $this->catalogRuleResourceModel = $catalogRuleResourceModel;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if the item has catalog price rules applied before validating the sales rule
     *
     * @param Rule $subject
     * @param bool $result
     * @param Item $item
     * @return bool
     */
    public function afterValidate(Rule $subject, $result, Item $item)
    {
        // If the rule is not valid or not a welcome discount, return the original result
        if (!$result || !$this->isWelcomeDiscount($subject)) {
            return $result;
        }

        // Check if the product has catalog price rules applied
        if ($this->hasProductCatalogPriceRule($item)) {
            return false; // Exclude the product from the welcome discount
        }

        return $result;
    }

    /**
     * Check if the rule is a welcome discount
     *
     * @param Rule $rule
     * @return bool
     */
    private function isWelcomeDiscount(Rule $rule)
    {
        // Check if the rule name contains "welcome" or if it has a specific coupon code
        // This can be customized based on how welcome discounts are identified in your system
        return (
            stripos($rule->getName(), 'welcome') !== false ||
            stripos($rule->getDescription(), 'welcome') !== false
        );
    }

    /**
     * Check if the product has catalog price rules applied
     *
     * @param Item $item
     * @return bool
     */
    private function hasProductCatalogPriceRule(Item $item)
    {
        $product = $item->getProduct();
        $websiteId = $this->storeManager->getStore($item->getStoreId())->getWebsiteId();
        $customerGroupId = $item->getQuote()->getCustomerGroupId();

        // Get the original price and the final price
        $originalPrice = $product->getPrice();
        $finalPrice = $product->getFinalPrice();

        // If the final price is less than the original price, check if it's due to a catalog rule
        if ($finalPrice < $originalPrice) {
            // Check if there are any catalog rules applied to this product
            $rulePrice = $this->catalogRuleResourceModel->getRulePrice(
                date('Y-m-d'),
                $websiteId,
                $customerGroupId,
                $product->getId()
            );

            // If there's a rule price and it matches the final price, the product has a catalog rule applied
            if ($rulePrice !== false && abs($rulePrice - $finalPrice) < 0.001) {
                return true;
            }
        }

        return false;
    }
}
