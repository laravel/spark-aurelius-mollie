<?php

namespace Laravel\Spark\Http\Requests\Settings\Teams\Subscription;

use Laravel\Cashier\Coupon\Contracts\CouponRepository as MollieCouponRepository;
use Laravel\Cashier\Exceptions\CouponException;
use Laravel\Spark\Contracts\Http\Requests\Settings\Teams\Subscription\CreateSubscriptionRequest as Contract;

class CreateMollieSubscriptionRequest extends CreateSubscriptionRequest implements Contract
{
    /**
     * Validate the coupon on the request.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function validateCoupon($validator)
    {
        /** @var MollieCouponRepository $mollieCoupons */
        $mollieCoupons = app(MollieCouponRepository::class);

        try {
            $coupon = $mollieCoupons->findOrFail($this->coupon);
            $subscription = $this->user()
                ->newSubscriptionForMandateId('fake-mandate-id', 'default', $this->plan)
                ->makeSubscription(); // not persisted yet

            $coupon->validateFor($subscription);
        } catch (CouponException $exception) {
            $validator->errors()->add('coupon', __('This coupon code is invalid.'));
        }
    }
}
