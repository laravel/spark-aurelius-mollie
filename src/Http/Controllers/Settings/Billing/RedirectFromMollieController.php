<?php

namespace Laravel\Spark\Http\Controllers\Settings\Billing;

use Laravel\Cashier\Order\Order;
use Laravel\Spark\Events\Subscription\UserSubscribed;
use Laravel\Spark\Events\Teams\Subscription\TeamSubscribed;
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
        $payment = $this->getPayment($paymentId);

        $fromRegistration = false;
        if(isset($payment->metadata, $payment->metadata->fromRegistration)) {
            $fromRegistration = $payment->metadata->fromRegistration;
        }

        $order = Order::findByPaymentId($paymentId);
        $owner = $order->owner;

        if (get_class($owner) === Spark::userModel()) {
            event(new UserSubscribed($owner, $owner->subscription(), $fromRegistration));
        } elseif (get_class($owner) === Spark::teamModel()) {
            event(new TeamSubscribed($owner, $owner->subscription(), $fromRegistration));
        }

        return $this->redirectWithPaymentStatus('new-subscription-status', $payment, 'subscription');
    }

    /**
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentMethod($paymentId)
    {
        return $this->redirectWithPaymentStatus(
            'payment-method-status',
            $this->getPayment($paymentId),
            'payment-method'
        );
    }

    /**
     * @param string $key
     * @param \Mollie\Api\Resources\Payment $payment
     * @param string $tab
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectWithPaymentStatus($key, $payment, $tab)
    {
        return redirect("/settings?{$key}={$payment->status}#/{$tab}");
    }

    /**
     * @param string $id
     * @return \Mollie\Api\Resources\Payment
     */
    protected function getPayment($id)
    {
        return mollie()->payments()->get($id);
    }
}
