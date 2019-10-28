<?php

namespace Laravel\Spark\Listeners\Subscription;

class RestartBillingCycleOnMollie
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        /** @var \Laravel\Cashier\Subscription $subscription */
        $subscription = $event->billable->subscription;

        if($subscription && $subscription->active()) {
            $subscription->restartCycle();
        }
    }
}
