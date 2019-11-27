<?php

namespace Laravel\Spark\Repositories;

class MolliePerformanceIndicatorsRepository extends PerformanceIndicatorsRepository
{
    /**
     * Get the subscriptions table name.
     *
     * @param $plan
     * @return string
     */
    protected function subscriptionsTable($plan)
    {
        return 'subscriptions';
    }

    /**
     * Get the plan column name.
     *
     * @return string
     */
    protected function planColumn()
    {
        return 'plan';
    }
}
