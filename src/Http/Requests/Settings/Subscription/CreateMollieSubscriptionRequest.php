<?php

namespace Laravel\Spark\Http\Requests\Settings\Subscription;

use Laravel\Cashier\Coupon\Contracts\CouponRepository as MollieCouponRepository;
use Laravel\Cashier\Exceptions\CouponException;
use Laravel\Spark\Spark;
use Illuminate\Support\Facades\Validator;
use Laravel\Spark\Http\Requests\ValidatesBillingAddresses;
use Laravel\Spark\Contracts\Http\Requests\Settings\Subscription\CreateSubscriptionRequest as Contract;

class CreateMollieSubscriptionRequest extends CreateSubscriptionRequest implements Contract
{
    use ValidatesBillingAddresses;

    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        $validator = Validator::make($this->all(), [
            'plan' => 'required|in:'.Spark::activePlanIdList(),
            'vat_id' => 'nullable|max:50|vat_id',
        ]);

        if (Spark::collectsBillingAddress()) {
            $this->validateBillingAddress($validator);
        }

        return $validator->after(function ($validator) {
            $this->validatePlanEligibility($validator);

            if ($this->coupon) {
                $this->validateCoupon($validator);
            }
        });
    }

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
