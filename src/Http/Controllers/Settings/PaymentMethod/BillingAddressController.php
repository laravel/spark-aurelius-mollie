<?php

namespace Laravel\Spark\Http\Controllers\Settings\PaymentMethod;

use Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdateBillingAddressRequest;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdateBillingAddress;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Spark;

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
    public function update(UpdateBillingAddressRequest $request)
    {
        return Spark::interact(UpdateBillingAddress::class, [
            $request->user(), $request->all(),
        ]);
    }
}
