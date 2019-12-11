<?php

namespace Laravel\Spark\Http\Controllers\Settings\Teams\PaymentMethod;

use Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdateBillingAddressRequest;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdateBillingAddress;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Spark;
use Laravel\Spark\Team;

class BillingAddressController extends Controller
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
     * Update the billing address for the user.
     *
     * @param  \Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdateBillingAddressRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillingAddressRequest $request, Team $team)
    {
        abort_unless($request->user()->ownsTeam($team), 403);

        return Spark::interact(UpdateBillingAddress::class, [
            $request->user(), $request->all(),
        ]);
    }
}
