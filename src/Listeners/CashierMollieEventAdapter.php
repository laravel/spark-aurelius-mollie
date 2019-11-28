<?php

namespace Laravel\Spark\Listeners;

use Laravel\Spark\Spark;

class CashierMollieEventAdapter
{
    /**
     * @var array Cashier events that are listened for in this mapper.
     */
    protected $cashierEvents = [
        'SubscriptionCancelled',
        'SubscriptionStarted',
        'SubscriptionPlanSwapped',
    ];

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        foreach ($this->cashierEvents as $cashierEvent) {
            $events->listen(
                'Laravel\Cashier\Events\\' . $cashierEvent,
                self::class . '@handle' . $cashierEvent
            );
        }
    }

    /**
     * @param \Laravel\Cashier\Events\SubscriptionCancelled $event
     */
    public function handleSubscriptionCancelled($event)
    {
        $billable = $event->subscription->owner;

        if($this->isTeam($billable)) {
            event(new \Laravel\Spark\Events\Teams\Subscription\SubscriptionCancelled($billable));
        } else {
            event(new \Laravel\Spark\Events\Subscription\SubscriptionCancelled($billable));
        }
    }

    /**
     * @param \Laravel\Cashier\Events\SubscriptionStarted $event
     */
    public function handleSubscriptionStarted($event)
    {
        $billable = $event->subscription->owner;

        if($this->isTeam($billable)) {
            event(new \Laravel\Spark\Events\Teams\Subscription\TeamSubscribed($billable, $billable->sparkPlan()));
        } else {
            event(new \Laravel\Spark\Events\Subscription\UserSubscribed($billable, $billable->sparkPlan(), false));
        }
    }

    /**
     * @param \Laravel\Cashier\Events\SubscriptionPlanSwapped $event
     */
    public function handleSubscriptionPlanSwapped($event)
    {
        $billable = $event->subscription->owner;

        if($this->isTeam($billable)) {
            event(new \Laravel\Spark\Events\Teams\Subscription\SubscriptionUpdated($billable));
        } else {
            event(new \Laravel\Spark\Events\Subscription\SubscriptionUpdated($billable));
        }
    }

    /**
     * @param $billable
     * @return bool
     */
    protected function isTeam($billable)
    {
        return get_class($billable) === Spark::teamModel();
    }
}
