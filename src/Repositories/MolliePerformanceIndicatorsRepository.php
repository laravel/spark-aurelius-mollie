<?php

namespace Laravel\Spark\Repositories;

use Laravel\Spark\Spark;
use Illuminate\Support\Facades\DB;
use Laravel\Spark\Team;
use Mollie\Api\Types\PaymentStatus;

class MolliePerformanceIndicatorsRepository extends PerformanceIndicatorsRepository
{

    /**
     * {@inheritdoc}
     */
    public function totalRevenueForUser($user)
    {
        $total = DB::table('orders')
            ->where('mollie_payment_status', PaymentStatus::STATUS_PAID)
            ->where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->sum('total');

        if(Spark::usesTeams()) {
            $total += DB::table('orders')
                ->where('mollie_payment_status', PaymentStatus::STATUS_PAID)
                ->where('owner_type', Team::class)
                ->whereIn('team_id', $user->teams->pluck('id')->all())
                ->sum('total');
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function totalVolume()
    {
        return DB::table('orders')
            ->where('mollie_payment_status', PaymentStatus::STATUS_PAID)
            ->sum('total') / 100;
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
