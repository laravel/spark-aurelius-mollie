<?php

namespace Laravel\Spark\Interactions\Settings\PaymentMethod;

use Laravel\Spark\Spark;
use Laravel\Spark\User;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Contracts\Repositories\TeamRepository;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod;

class UpdateMollieBillingAddress implements UpdatePaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function handle($billable, array $data)
    {
        if (Spark::collectsBillingAddress()) {
            Spark::call(
                $this->updateBillingAddressMethod($billable),
                [$billable, $data]
            );
        }
    }

    /**
     * Get the repository class name for a given billable instance.
     *
     * @param  mixed  $billable
     * @return string
     */
    protected function updateBillingAddressMethod($billable)
    {
        return ($billable instanceof User
                    ? UserRepository::class
                    : TeamRepository::class).'@updateBillingAddress';
    }
}
