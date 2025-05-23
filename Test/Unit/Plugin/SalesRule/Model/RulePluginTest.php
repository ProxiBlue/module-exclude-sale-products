<?php
/**
 * @category  ProxiBlue
 * @package   ProxiBlue_ExcludeSaleProducts
 * @author    ProxiBlue
 * @copyright Copyright (c) 2023 ProxiBlue (https://www.proxiblue.com.au)
 */

namespace ProxiBlue\ExcludeSaleProducts\Test\Unit\Plugin\SalesRule\Model;

use Magento\CatalogRule\Model\ResourceModel\Rule as CatalogRuleResourceModel;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use ProxiBlue\ExcludeSaleProducts\Plugin\SalesRule\Model\RulePlugin;

/**
 * Unit test for RulePlugin
 */
class RulePluginTest extends TestCase
{
    /**
     * @var CatalogRuleResourceModel|\PHPUnit\Framework\MockObject\MockObject
     */
    private $catalogRuleResourceModelMock;

    /**
     * @var StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    /**
     * @var Rule|\PHPUnit\Framework\MockObject\MockObject
     */
    private $ruleMock;

    /**
     * @var Item|\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemMock;

    /**
     * @var Quote|\PHPUnit\Framework\MockObject\MockObject
     */
    private $quoteMock;

    /**
     * @var Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productMock;

    /**
     * @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeMock;

    /**
     * @var RulePlugin
     */
    private $rulePlugin;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->catalogRuleResourceModelMock = $this->createMock(CatalogRuleResourceModel::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->ruleMock = $this->createMock(Rule::class);
        $this->itemMock = $this->createMock(Item::class);
        $this->quoteMock = $this->createMock(Quote::class);
        $this->productMock = $this->createMock(Product::class);
        $this->storeMock = $this->createMock(StoreInterface::class);

        $this->rulePlugin = new RulePlugin(
            $this->catalogRuleResourceModelMock,
            $this->storeManagerMock
        );
    }

    /**
     * Test afterValidate method when rule is not a welcome discount
     */
    public function testAfterValidateWhenRuleIsNotWelcomeDiscount()
    {
        $this->ruleMock->expects($this->once())
            ->method('getName')
            ->willReturn('Regular Discount');

        $this->ruleMock->expects($this->once())
            ->method('getDescription')
            ->willReturn('Regular discount description');

        $result = $this->rulePlugin->afterValidate($this->ruleMock, true, $this->itemMock);
        $this->assertTrue($result);
    }

    /**
     * Test afterValidate method when rule is a welcome discount but product has no catalog price rule
     */
    public function testAfterValidateWhenRuleIsWelcomeDiscountButProductHasNoCatalogPriceRule()
    {
        $this->ruleMock->expects($this->once())
            ->method('getName')
            ->willReturn('Welcome Discount');

        $this->ruleMock->expects($this->any())
            ->method('getDescription')
            ->willReturn('Welcome discount description');

        $this->itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->productMock);

        $this->itemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn(1);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(1)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->itemMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(1);

        $this->productMock->expects($this->once())
            ->method('getPrice')
            ->willReturn(100);

        $this->productMock->expects($this->once())
            ->method('getFinalPrice')
            ->willReturn(100);

        $result = $this->rulePlugin->afterValidate($this->ruleMock, true, $this->itemMock);
        $this->assertTrue($result);
    }

    /**
     * Test afterValidate method when rule is a welcome discount and product has catalog price rule
     */
    public function testAfterValidateWhenRuleIsWelcomeDiscountAndProductHasCatalogPriceRule()
    {
        $this->ruleMock->expects($this->once())
            ->method('getName')
            ->willReturn('Welcome Discount');

        $this->ruleMock->expects($this->any())
            ->method('getDescription')
            ->willReturn('Welcome discount description');

        $this->itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->productMock);

        $this->itemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn(1);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(1)
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->itemMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(1);

        $this->productMock->expects($this->once())
            ->method('getPrice')
            ->willReturn(100);

        $this->productMock->expects($this->once())
            ->method('getFinalPrice')
            ->willReturn(80);

        $this->productMock->expects($this->once())
            ->method('getId')
            ->willReturn(123);

        $this->catalogRuleResourceModelMock->expects($this->once())
            ->method('getRulePrice')
            ->with(
                $this->anything(),
                1,
                1,
                123
            )
            ->willReturn(80);

        $result = $this->rulePlugin->afterValidate($this->ruleMock, true, $this->itemMock);
        $this->assertFalse($result);
    }
}
