<?php

namespace Laravel\Spark\Http\Controllers\Settings\Billing;

use Laravel\Cashier\Order\Order;
use Laravel\Spark\Spark;

class RedirectFromMollieController
{
    /**
     * Handle a redirect from Mollie and redirect the user
     *
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function newSubscription($paymentId)
    {
        return $this->redirectWithPaymentStatus($paymentId, 'new-subscription-status', 'subscription');
    }

    /**
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentMethod($paymentId)
    {
        return $this->redirectWithPaymentStatus($paymentId, 'payment-method-status', 'payment-method');
    }

    /**
     * @param $paymentId
     * @param $key
     * @param $tab
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectWithPaymentStatus($paymentId, $key, $tab)
    {
        $owner = Order::findByPaymentIdOrFail($paymentId)->owner;
        $payment = mollie()->payments()->get($paymentId);

        return get_class($owner) === Spark::teamModel()
            ? redirect("/settings/teams/{$owner->id}?{$key}={$payment->status}#/{$tab}")
            : redirect("/settings?{$key}={$payment->status}#/{$tab}");
    }
}
