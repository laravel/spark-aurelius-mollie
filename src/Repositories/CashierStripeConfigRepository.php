<?php

namespace Laravel\Spark\Repositories;

use Laravel\Spark\Contracts\Repositories\CashierConfigRepository;

class CashierStripeConfigRepository implements CashierConfigRepository
{
    /**
     * @inheritDoc
     */
    public function currency()
    {
        return config('cashier.currency');
    }

    /**
     * @inheritDoc
     */
    public function currencyLocale()
    {
        return config('cashier.currency_locale');
    }

    /**
     * @inheritDoc
     */
    public function authKey()
    {
        return config('cashier.key');
    }

    /**
     * @inheritDoc
     */
    public function path()
    {
        return config('cashier.path');
    }
}
