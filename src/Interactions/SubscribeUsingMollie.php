<?php

namespace Laravel\Spark\Interactions;

use Illuminate\Support\Arr;
use Laravel\Cashier\SubscriptionBuilder\RedirectToCheckoutResponse;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Events\Subscription\UserSubscribed;
use Laravel\Spark\Spark;

class SubscribeUsingMollie extends Subscribe
{
    /**
     * {@inheritdoc}
     */
    public function handle($user, $plan, $fromRegistration, array $data)
    {
        // We need to check if this application is storing billing addresses and if so
        // we will update the billing address in the database so that any tax information
        // on the user will be up to date via the taxPercentage method on the billable.
        if (Spark::collectsBillingAddress()) {
            Spark::call(
                UserRepository::class.'@updateBillingAddress',
                [$user, $data]
            );
        }

        // If this application collects European VAT, we will store the VAT ID that was sent
        // with the request. It is used to determine if the VAT should get charged at all
        // when billing the customer. When it is present, VAT is not typically charged.
        if (Spark::collectsEuropeanVat()) {
            Spark::call(
                UserRepository::class.'@updateVatId',
                [$user, Arr::get($data, 'vat_id')]
            );
        }

        $subscription = $this->getSubscriptionBuilder($user, $plan, $fromRegistration, $data);

        // Here we will check if we need to skip trial or set trial days on the subscription
        // when creating it on the provider. By default, we will skip the trial when this
        // interaction isn't from registration since they have already usually trialed.
        if (! $fromRegistration && $user->hasEverSubscribedTo('default', $plan->id)) {
            $subscription->skipTrial();
        } elseif ($plan->trialDays > 0) {
            $subscription->trialDays($plan->trialDays);
        }

        if (isset($data['coupon'])) {
            $subscription->withCoupon($data['coupon']);
        }

        if (Spark::chargesUsersPerTeam() || Spark::chargesUsersPerSeat()) {
            $subscription->quantity(Spark::seatsCount($user));
        }

        // Here we will create the actual subscription on the service and fire off the event
        // letting other listeners know a user has subscribed, which will allow any hooks
        // to fire that need to send the subscription data to any external metrics app.
        $response = $subscription->create();

        // Cashier will attempt to redirect the customer to Mollie's checkout if the customer
        // has no valid payment mandate yet.
        if(is_a($response, RedirectToCheckoutResponse::class)) {

            /** @var $response \Laravel\Cashier\SubscriptionBuilder\RedirectToCheckoutResponse */
            $payment = $response->payment();
            $payment->redirectUrl = url('/redirects/mollie/new-subscription/' . $payment->id);
            $payment->metadata->fromRegistration = $fromRegistration;
            $payment->update();

            return response([
                'subscribeViaCheckout' => true,
                'checkoutUrl' => $response->getTargetUrl(),
            ]);
        }

        $user = $user->fresh();
        event(new UserSubscribed($user, $plan, $fromRegistration));

        return $user;
    }

    /**
     * Get the appropriate SubscriptionBuilder for the scenario: go through Mollie's checkout or not.
     *
     * @param $user
     * @param $plan
     * @param $fromRegistration
     * @param array $data
     * @return \Laravel\Cashier\SubscriptionBuilder\Contracts\SubscriptionBuilder
     */
    protected function getSubscriptionBuilder($user, $plan, $fromRegistration, array $data)
    {
        return isset($data['use_existing_payment_method']) && $data['use_existing_payment_method']
            ? $user->newSubscriptionForMandateId($user->mollieMandateId(), 'default', $plan->id)
            : $user->newSubscriptionViaMollieCheckout('default', $plan->id);

        // Let Cashier decide whether to let the customer go through checkout
        return $user->newSubscription('default', $plan->id);
    }
}
