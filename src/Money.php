<?php

/**
 * This file is part of the ferdiunal/money library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ferdi ÜNAL <ferdiunal@outlook.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @see readme.md for Documentation
 * @see https://packagist.org/packages/ferdiunal/money Packagist
 * @see https://github.com/ferdiunal/money GitHub
 */

namespace Ferdiunal\Money;

class Money
{
    /**
     * Singleton instance of Money class.
     * @var Money|null
     */
    private static ?Money $instance = null;

    /**
     * The monetary value represented by this instance.
     */
    protected float $amount;

    /**
     * The tax rate to be applied to the monetary value.
     */
    protected int $taxRate = 18;

    /**
     * The number of decimal places to be used when displaying the monetary value.
     */
    protected int $decimals = 2;

    /**
     * The calculated tax amount to be added to the monetary value.
     */
    protected float $taxAmount = 0;

    /**
     * The ISO 4217 currency code used for formatting the monetary value in the desired locale.
     */
    protected string $localeCode = 'TRL';

    /**
     * A flag indicating whether the locale options should be used in formatting the monetary value.
     */
    protected bool $localeActive = false;

    /**
     * The prefix to be displayed before the monetary value in the desired locale.
     */
    protected ?string $localePrefix;

    /**
     * The suffix to be displayed after the monetary value in the desired locale.
     */
    protected ?string $localeSuffix;

    /**
     * The position where the locale code should be displayed, either 'prefix' or 'suffix'.
     */
    protected string $localePosition = 'prefix';

    /**
     * Creates a new Money instance with the given monetary value.
     */
    public static function make(float $amount): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        static::$instance->amount = $amount;

        return static::$instance;
    }

    /**
     * Sets the number of decimal places to be used when displaying the monetary value.
     * Returns the current Money instance for method chaining.
     */
    public function setDecimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * Enables or disables the use of the locale options when formatting the monetary value.
     * Returns the current Money instance for method chaining.
     */
    public function setLocaleActive(bool $active): static
    {
        $this->localeActive = $active;

        return $this;
    }

    /**
     * Sets the ISO 4217 currency code to be used for formatting the monetary value in the desired locale.
     * Returns the current Money instance for method chaining.
     */
    public function setLocaleCode(string $code): static
    {
        $this->localeCode = $code;

        return $this;
    }

    /**
     * Sets the position where the locale code should be displayed, either 'prefix' or 'suffix'.
     * Returns the current Money instance for method chaining.
     */
    public function setLocalePosition(string $position): static
    {
        if (!in_array($position, ['prefix', 'suffix'])) {
            $position = 'prefix';
        }

        $this->localePosition = $position;

        $this->updateLocalePrefixSuffix();

        return $this;
    }

    /**
     * Adds the given variable(s) to the monetary value.
     * Allows multiple variables to be added by separating them with commas.
     * Returns the current Money instance for method chaining.
     */
    public function sum(...$numbers): static
    {
        foreach ($numbers as $number) {
            $this->amount += $this->clear($number);
        }

        return $this;
    }

    /**
     * Subtracts the given variable(s) from the monetary value.
     * Allows multiple variables to be subtracted by separating them with commas.
     * Returns the current Money instance for method chaining.
     */
    public function subtract(...$numbers): static
    {
        foreach ($numbers as $number) {
            $this->amount -= $this->clear($number);
        }

        return $this;
    }

    /**
     * Multiplies the monetary value by the given factor.
     * Returns the current Money instance for method chaining.
     */
    public function multiply(float $number): static
    {
        $this->amount *= $this->clear($number);

        return $this;
    }

    /**
     * Divides the monetary value by the given factor.
     * Returns the current Money instance for method chaining.
     */
    public function divide(float $number): static
    {
        if ($number != 0) {
            $this->amount /= $this->clear($number);
        } else {
            $this->amount = 0;
        }

        return $this;
    }

    /**
     * Adds the calculated tax amount to the monetary value.
     * If no tax rate is specified, the default rate of 18% is used.
     * Returns the current Money instance for method chaining.
     */
    public function addTax(?int $percent = null): static
    {
        $this->taxAmount = $this->clear($this->amount) * ($this->clear($percent ?? $this->taxRate) / 100);

        $this->amount += $this->taxAmount;

        return $this;
    }

    /**
     * Adds a percent amount discount to the monetary value.
     * @param float $percent the percentage of discount to apply
     * @return static $this the current Money instance for method chaining
     */
    protected function addPercentDiscount(float $percent): static
    {
        $discount = $this->clear($this->amount) * ($this->clear($percent) / 100);
        $this->amount -= $discount;
        return $this;
    }

    /**
     *  Adds a fixed amount discount to the monetary value.
     *  @param float $amount the amount of discount to apply
     *  @return static $this the current Money instance for method chaining
     */
    protected function addFixedDiscount(float $amount): static
    {
        $this->amount -= $this->clear($amount);
        return $this;
    }

    /**
     * Adds a discount to the monetary value.
     * @param float $value The amount of discount to apply.
     * @param bool $isFixed Determines whether the discount amount is in percentage or fixed value.
     * @return static $this The current Money instance for method chaining.
     */
    public function addDiscount(float $value, bool $isFixed = false): static
    {
        if ($isFixed) {
            return $this->addFixedDiscount($value);
        }

        return $this->addPercentDiscount($value);
    }

    /**
     * Removes the calculated tax amount from the monetary value.
     * If no tax rate is specified, the default rate of 18% is used.
     * Returns the current Money instance for method chaining.
     */
    public function removeTax(?int $percent = null): static
    {
        $this->taxAmount = $this->amount - $this->clear($this->amount) / (1 + ($this->clear($percent ?? $this->taxRate) / 100));

        $this->amount -= $this->taxAmount;

        return $this;
    }

    /**
     * Gets the monetary value as a string formatted according to the current locale options.
     */
    public function get(): string
    {
        $amount = $this->clear($this->amount);
        $amount = number_format($amount, $this->decimals, ',', '.');

        if ($this->localeActive) {
            if ($this->localePosition == "prefix") {
                return sprintf('%s%s', $this->localeCode, $amount);
            } else {
                return sprintf('%s%s', $amount, $this->localeCode);
            }
        }

        return (string) $amount;
    }

    /**
     * Gets the calculated tax amount as a floating-point number.
     */
    public function getTax(): float
    {
        return $this->taxAmount;
    }

    /**
     * Gets the monetary value and the calculated tax amount as an array.
     */
    public function all(): array
    {
        $amount = $this->clear($this->amount);
        $amount = number_format($amount, $this->decimals, ',', '.');

        return [
            'amount' => $this->get(),
            'tax' => $this->getTax(),
        ];
    }

    /**
     * Updates the locale prefix and suffix based on the desired position.
     */
    protected function updateLocalePrefixSuffix(): void
    {
        if ($this->localePosition === 'prefix') {
            $this->localePrefix = $this->localeCode;
            $this->localeSuffix = null;
        } else {
            $this->localePrefix = null;
            $this->localeSuffix = $this->localeCode;
        }
    }

    /**
     * Removes commas from the given number to make it available for the arithmetic operations.
     */
    protected function clear(float $number): float
    {
        return (float) str_replace(',', '', $number);
    }
}
