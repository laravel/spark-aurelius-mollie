<?php

namespace Laravel\Spark\Repositories;

use Carbon\Carbon;
use Laravel\Spark\Plan;
use Laravel\Spark\Spark;
use Laravel\Spark\TeamPlan;
use Illuminate\Support\Facades\DB;
use Laravel\Spark\Contracts\Repositories\PerformanceIndicatorsRepository as Contract;

class PerformanceIndicatorsRepository implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function all($take = 60)
    {
        return array_reverse(
            DB::table('performance_indicators')->orderBy('created_at', 'desc')->take($take)->get()->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forDate(Carbon $date)
    {
        return DB::table('performance_indicators')->where('created_at', $date)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function totalRevenueForUser($user)
    {
        $teamIds = Spark::usesTeams() ? $user->teams->pluck('id')->all() : [];

        return DB::table('invoices')
                        ->where('user_id', $user->id)
                        ->orWhereIn('team_id', $teamIds)
                        ->sum('total');
    }

    /**
     * {@inheritdoc}
     */
    public function totalVolume()
    {
        return DB::table('invoices')->sum('total');
    }

    /**
     * {@inheritdoc}
     */
    public function yearlyRecurringRevenue()
    {
        return $this->monthlyRecurringRevenue() * 12;
    }

    /**
     * Get the monthly recurring revenue.
     *
     * @return float
     */
    public function monthlyRecurringRevenue()
    {
        return $this->recurringRevenueByInterval('monthly') +
              ($this->recurringRevenueByInterval('yearly') / 12);
    }

    /**
     * Get the recurring revenue for the given interval.
     *
     * @param  string  $interval
     * @return float
     */
    protected function recurringRevenueByInterval($interval)
    {
        $total = 0;

        $plans = $interval === 'monthly' ? Spark::allMonthlyPlans() : Spark::allYearlyPlans();

        foreach ($plans as $plan) {
            $total += DB::table($this->subscriptionsTable($plan))
                            ->where($this->planColumn(), $plan->id)
                            ->where(function ($query) {
                                $query->whereNull('trial_ends_at')
                                      ->orWhere('trial_ends_at', '<=', Carbon::now());
                            })
                            ->whereNull('ends_at')
                            ->sum('quantity') * $plan->price;
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribers(Plan $plan)
    {
        if ($plan->price === 0) {
            return $this->freePlanSubscribers($plan);
        }

        return DB::table($this->subscriptionsTable($plan))
                            ->where($this->planColumn(), $plan->id)
                            ->where(function ($query) {
                                $query->whereNull('trial_ends_at')
                                      ->orWhere('trial_ends_at', '<=', Carbon::now());
                            })
                            ->whereNull('ends_at')
                            ->count();
    }

    /**
     * Get the subscriber count for the given free plan.
     *
     * @param  \Laravel\Spark\Plan  $plan
     * @return int
     */
    public function freePlanSubscribers($plan)
    {
        return DB::table($this->billableTable($plan))
                            ->whereNull('current_billing_plan')
                            ->where('trial_ends_at', '<', Carbon::now())
                            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function trialing(Plan $plan)
    {
        return DB::table($this->subscriptionsTable($plan))
                        ->where($this->planColumn(), $plan->id)
                        ->where('trial_ends_at', '>', Carbon::now())
                        ->whereNull('ends_at')
                        ->count();
    }

    /**
     * Get the billable table name.
     *
     * @param $plan
     * @return string
     */
    protected function billableTable($plan)
    {
        return $plan instanceof TeamPlan ? 'teams' : 'users';
    }

    /**
     * Get the subscriptions table name.
     *
     * @param $plan
     * @return string
     */
    protected function subscriptionsTable($plan)
    {
        return $plan instanceof TeamPlan ? 'team_subscriptions' : 'subscriptions';
    }

    /**
     * Get the plan column name.
     *
     * @return string
     */
    protected function planColumn()
    {
        return 'stripe_plan';
    }
}
