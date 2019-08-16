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
        return $this->redirectWithPaymentStatus($paymentId, 'subscription');
    }

    /**
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentMethod($paymentId)
    {
        return $this->redirectWithPaymentStatus($paymentId, 'payment-method');
    }

    /**
     * @param $paymentId
     * @param $tab
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectWithPaymentStatus($paymentId, $tab)
    {
        $status = mollie()->payments()->get($paymentId)->status;

        return redirect("/settings?payment_status={$status}#/{$tab}");
    }
    
}
