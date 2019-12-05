<?php

namespace Laravel\Spark\Contracts\Repositories;

interface CashierConfigRepository
{
    /**
     * @return string
     */
    public function currency();

    /**
     * @return string
     */
    public function currencyLocale();

    /**
     * @return string|null
     */
    public function authKey();

    /**
     * @return string|null
     */
    public function path();
}
