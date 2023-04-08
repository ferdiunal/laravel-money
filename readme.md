# ferdiunal/money

[![Latest Version][badge-release]][release]
[![Total Downloads][badge-downloads]][downloads]

This package is a fork of the sineld/money repository, with added discount and data types.

ferdiunal/money is a PHP library designed to make working with money easier! There are no static properties or methods. Any number you pass to the class will automatically be prepared for mathematical operations. The class uses , for thousands and . for decimals.

This package can be used with any framework or spaghetti application. If you encounter any issues, feel free to reach out to me by email.

## Installation

Via Composer

``` bash
$ composer require ferdiunal/money
```

Add the following use statement to the top of your file:

``` bash
use Ferdiunal\Money\Money;
```

Then, start using the library!

## Non-Composer Users

If you are not using Composer, simply copy the Money.php file located in the src folder to your project and begin using the library. There are no extra dependencies.

### Request method aliases

Here are the parameters you can use with methods:

##### money->setDecimals(default = 2)
##### money->addTax(default = 18)
##### money->removeTax(default = 18)
##### money->setLocaleActive(default = false)
##### money->setLocaleCode(default = TRL)
##### money->setLocalePosition(default = prefix, (use "suffix" instead of reverse))

## Usage Examples

In the following code example, a currency object is created using the `Ferdiunal\Money\Money` class. First, a currency object is created based on the specified numeric value. Then, another numeric value is added, subtracted, multiplied, and divided to the currency object. After adding a percentage-based tax, a fixed discount and a percentage-based discount are added. Finally, tax and discount are removed based on the calculations, and the currency object is formatted and retrieved using the `get()` method. The `all()` method returns the currency and tax amount as an array. The `getTax()` method returns the calculated tax amount. The `getDiscount()` method returns the calculated discount amount.

``` php
<?php

use Ferdiunal\Money\Money;

// Create a new Money instance with a value of 100.50
$money = Money::make(100.50);

// Add 50.25 to the Money instance
$money->sum(50.25);

// Subtract 10.50 from the Money instance
$money->subtract(10.50);

// Multiply the Money instance by 2
$money->multiply(2);

// Divide the Money instance by 3
$money->divide(3);

// Add a 20% tax to the Money instance
$money->addTax(20);

// Add a fixed discount of 15 to the Money instance
$money->addDiscount(15, true);

// Add a 10% discount to the Money instance
$money->addDiscount(10);

// Remove the 20% tax from the Money instance
$money->removeTax(20);

// Enable locale usage and set the locale code to USD
$money->setLocaleActive(true)->setLocaleCode('USD');

// Get the Money instance as a formatted string
$formattedMoney = $money->get();

// Get the tax amount as a formatted string
$taxAmount = $money->getTax();

// Get the discount amount as a formatted string
$discountAmount = $money->getDiscount();

// Get the Money instance and tax amount as an array
$allData = $money->all();

// Output the formatted string
echo $formattedMoney; // $208.00

// Output the tax amount
echo $taxAmount; // $31.20

// Output the discount amount
echo $discountAmount; // $28.20

// Output the Money instance and tax amount as an array
print_r($allData); // Array ( [money] => 179.80 [tax] => 31.20, [discount] => 28.20 )

```


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Sinan Eldem](https://www.sinaneldem.com.tr)
- [Ferdi ÜNAL](https://twitter.com/__ferdiunal)

## License

Please see the [license file](license.md) for more information.

[badge-release]: https://img.shields.io/packagist/v/ferdiunal/money.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/ferdiunal/money.svg?style=flat-square

[release]: https://packagist.org/packages/ferdiunal/money
[downloads]: https://packagist.org/packages/ferdiunal/money
