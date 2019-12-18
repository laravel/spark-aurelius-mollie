<?php

namespace Laravel\Spark\Http\Controllers\Settings\PaymentMethod;

use Laravel\Spark\Spark;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod;
use Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdatePaymentMethodRequest;

class PaymentMethodController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update the payment method for the user.
     *
     * @param  \Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdatePaymentMethodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentMethodRequest $request)
    {
        $user = $request->user();

        abort_if(
            Spark::billsUsingMollie() && Spark::collectsEuropeanVat() && empty($user->billing_country),
            422,
            __('Please first register your billing address.')
        );

        return Spark::interact(UpdatePaymentMethod::class, [
            $user, $request->all(),
        ]);
    }
}
