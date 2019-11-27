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

        $order = Order::findByPaymentIdOrFail($paymentId);
        $owner = $order->owner;

        // ======= TODO START move to event mapper =======
        $fromRegistration = false;
        if(isset($payment->metadata, $payment->metadata->fromRegistration)) {
            $fromRegistration = $payment->metadata->fromRegistration;
        }

        if ($this->isTeam($owner)) {
            event(new TeamSubscribed($owner, $owner->subscription(), $fromRegistration));
        } else {
            event(new UserSubscribed($owner, $owner->subscription(), $fromRegistration));
        }
        // ======= TODO END move to event mapper =======

        return $this->redirectWithPaymentStatus($owner, 'new-subscription-status', $payment, 'subscription');
    }

    /**
     * @param $paymentId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentMethod($paymentId)
    {
        return $this->redirectWithPaymentStatus(
            Order::findByPaymentIdOrFail($paymentId)->owner,
            'payment-method-status',
            $this->getPayment($paymentId),
            'payment-method'
        );
    }

    /**
     * @param $owner
     * @param $key
     * @param $payment
     * @param $tab
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectWithPaymentStatus($owner, $key, $payment, $tab)
    {
        return $this->isTeam($owner)
            ? redirect("/settings/teams/{$owner->id}?{$key}={$payment->status}#/{$tab}")
            : redirect("/settings?{$key}={$payment->status}#/{$tab}");
    }

    /**
     * @param string $id
     * @return \Mollie\Api\Resources\Payment
     */
    protected function getPayment($id)
    {
        return mollie()->payments()->get($id);
    }

    /**
     * @param $billable
     * @return bool
     */
    protected function isTeam($billable)
    {
        return get_class($billable) === Spark::teamModel();
    }
}
