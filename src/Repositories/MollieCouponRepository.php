<?php

namespace Laravel\Spark\Repositories;

use Laravel\Cashier\Coupon\Contracts\CouponRepository as CashierCouponRepository;
use Laravel\Cashier\Exceptions\CouponException as CashierCouponException;
use Laravel\Spark\Contracts\Repositories\CouponRepository as Contract;
use Laravel\Spark\Coupon as SparkCoupon;
use Laravel\Spark\Spark;

class MollieCouponRepository implements Contract
{
    /** @var \Laravel\Cashier\Coupon\Contracts\CouponRepository */
    protected $cashierCoupons;

    /**
     * MollieCouponRepository constructor.
     *
     * @param \Laravel\Cashier\Coupon\Contracts\CouponRepository $cashierCoupons
     */
    public function __construct(CashierCouponRepository $cashierCoupons)
    {
        $this->cashierCoupons = $cashierCoupons;
    }

    /**
     * Determine if the given coupon code is valid.
     *
     * @param string $code
     * @return bool
     */
    public function valid($code)
    {
        $cashierCoupon = $this->cashierCoupons->find($code);
        $subscription = $this->billable()->subscription();

        if(is_null($cashierCoupon) || is_null($subscription)) {
            return false;
        }

        try {
            $cashierCoupon->validateFor($subscription);
        } catch (CashierCouponException $e) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the coupon may be redeemed by an existing customer.
     *
     * @param string $code
     * @return bool
     */
    public function canBeRedeemed($code)
    {
        return $this->valid($code) && Spark::promotion() !== $code;
    }

    /**
     * Get the coupon data for the given code.
     *
     * @param string $code
     * @return \Laravel\Spark\Coupon|null
     */
    public function find($code)
    {
        $coupon = $this->cashierCoupons->find($code);

        return $coupon ? $this->toCoupon($coupon, $this->billable()) : null;
    }

    /**
     * Get the current coupon for the given billable entity.
     *
     * @param mixed $billable
     * @return \Laravel\Spark\Coupon|null
     */
    public function forBillable($billable)
    {
        /** @var \Laravel\Cashier\Coupon\RedeemedCoupon $redeemedCoupon */
        $redeemedCoupon = $billable->redeemedCoupons()->active()->first();

        return $redeemedCoupon ? $this->toCoupon($redeemedCoupon->coupon(), $billable, $redeemedCoupon) : null;
    }

    /**
     * Convert the given Cashier Mollie coupon into a Spark Coupon instance.
     *
     * @param \Laravel\Cashier\Coupon\Coupon $cashierCoupon
     * @param mixed $billable
     * @param \Laravel\Cashier\Coupon\RedeemedCoupon $redeemedCoupon
     * @return \Laravel\Spark\Coupon
     */
    protected function toCoupon($cashierCoupon, $billable, $redeemedCoupon = null)
    {
        $times = $redeemedCoupon ? $redeemedCoupon->times_left : $cashierCoupon->times();
        $occurence = $times == 1 ? 'once' : 'repeating';
        $forOrderItems = $billable->orderItems()->unprocessed()->get();
        $amountOff = -(
            $cashierCoupon->handler()->getDiscountOrderItems($forOrderItems)->sum('total')
        );


        return new SparkCoupon($occurence, $times, $amountOff);
    }

    /**
     * Get the current user of the application.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function billable()
    {
        return app()->make(UserRepository::class)->current();
    }
}
