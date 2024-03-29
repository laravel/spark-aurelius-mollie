<?php

namespace Laravel\Spark\Http\Controllers\Settings\Teams\PaymentMethod;

use Laravel\Spark\Team;
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
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentMethodRequest $request, Team $team)
    {
        abort_unless($request->user()->ownsTeam($team), 403);

        abort_if(
            Spark::billsUsingMollie() && Spark::collectsEuropeanVat() && empty($team->billing_country),
            422,
            __("Please first register your team's billing address.")
        );

        return Spark::interact(UpdatePaymentMethod::class, [
            $team, $request->all(),
        ]);
    }
}
