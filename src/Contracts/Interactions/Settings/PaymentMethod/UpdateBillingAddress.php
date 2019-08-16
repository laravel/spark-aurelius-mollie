<?php

namespace Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod;

interface UpdateBillingAddress
{
    /**
     * Update the billable entity's billing address.
     *
     * @param  mixed  $billable
     * @param  array  $data
     * @return void
     */
    public function handle($billable, array $data);
}
