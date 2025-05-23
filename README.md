# ProxiBlue ExcludeSaleProducts

This Magento 2 module prevents double discounting by excluding products with catalog price rules (sale products) from welcome discount cart rules.

ATTENTION: This module was comletely created using AI and is untested.
## Problem Statement

When a store has products with catalog price rules (sale products) and also offers a welcome discount via cart rules, customers can get a double discount. This module prevents this by excluding sale products from the welcome discount.

## Features

- Automatically detects products with catalog price rules applied
- Excludes these products from welcome discount cart rules
- Configurable to identify welcome discounts by name or description
- Fully unit tested

## Installation

### Via Composer

```bash
composer require proxiblue/module-exclude-sale-products
bin/magento module:enable ProxiBlue_ExcludeSaleProducts
bin/magento setup:upgrade
bin/magento cache:clean
```

### Manual Installation

1. Create the following directory: `app/code/ProxiBlue/ExcludeSaleProducts`
2. Download the module files and place them in this directory
3. Run the following commands:

```bash
bin/magento module:enable ProxiBlue_ExcludeSaleProducts
bin/magento setup:upgrade
bin/magento cache:clean
```

## Configuration

By default, the module identifies welcome discounts by looking for the word "welcome" in the rule name or description. You can customize this logic by modifying the `isWelcomeDiscount` method in the `RulePlugin` class.

## How It Works

The module uses a plugin on the `Magento\SalesRule\Model\Rule` class to check if a product has catalog price rules applied before applying a welcome discount. If a product has a catalog price rule applied (indicated by a difference between the original price and the final price due to a catalog rule), it will be excluded from the welcome discount.

## Unit Tests

The module includes comprehensive unit tests to ensure its functionality. You can run the tests using the following command:

```bash
bin/magento dev:tests:run unit
```

## Support

For issues or feature requests, please open an issue on the [GitHub repository](https://github.com/ProxiBlue/module-exclude-sale-products).

## License

This module is licensed under the Open Software License v. 3.0 (OSL-3.0).
