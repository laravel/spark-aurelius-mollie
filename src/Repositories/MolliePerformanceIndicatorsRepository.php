<?php

namespace Laravel\Spark\Repositories;

class MolliePerformanceIndicatorsRepository extends PerformanceIndicatorsRepository
{
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
