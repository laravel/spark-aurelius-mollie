<?php

namespace Laravel\Spark\Http\Controllers\Settings\Billing;

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
        return $this->redirectWithPaymentStatus('new-subscription-status', $paymentId, 'subscription');
    }

    /**
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentMethod($paymentId)
    {
        return $this->redirectWithPaymentStatus('payment-method-status', $paymentId, 'payment-method');
    }

    /**
     * @param $key
     * @param $paymentId
     * @param $tab
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectWithPaymentStatus($key, $paymentId, $tab)
    {
        $status = mollie()->payments()->get($paymentId)->status;

        return redirect("/settings?{$key}={$status}#/{$tab}");
    }

}
