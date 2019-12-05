<?php

namespace Laravel\Spark\Repositories;

use Laravel\Cashier\Cashier;
use Laravel\Spark\Contracts\Repositories\CashierConfigRepository;

class CashierMollieConfigRepository implements CashierConfigRepository
{
    /**
     * @inheritDoc
     */
    public function currency()
    {
        return Cashier::usesCurrency();
    }

    /**
     * @inheritDoc
     */
    public function currencyLocale()
    {
        return substr(Cashier::usesCurrencyLocale(), 0, 2);
    }

    /**
     * @inheritDoc
     */
    public function authKey()
    {
        return null; // Not used by Cashier Mollie
    }

    /**
     * @inheritDoc
     */
    public function path()
    {
        return null; // Not used by Cashier Mollie
    }
}
