<?php

namespace Laravel\Spark\Listeners\Profile;

class UpdateContactInformationOnMollie
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Spark\Events\Profile\ContactInformationUpdated  $event
     */
    public function handle($event)
    {
        $user = $event->user;
        if (! $user->hasBillingProvider()) {
            return;
        }

        /** @var \Mollie\Api\Resources\Customer $customer */
        $customer = $user->asMollieCustomer();

        foreach ($user->mollieCustomerFields() as $key => $value) {
            if (property_exists($customer, $key)) {
                $customer->$key = $value;
            }
        }

        $customer->update();
    }
}
