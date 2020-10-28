<?php

namespace Laravel\Spark\Interactions;

use Illuminate\Support\Arr;
use Laravel\Cashier\SubscriptionBuilder\RedirectToCheckoutResponse;
use Laravel\Spark\Contracts\Interactions\SubscribeTeam as Contract;
use Laravel\Spark\Contracts\Repositories\TeamRepository;
use Laravel\Spark\Events\Teams\Subscription\TeamSubscribed;
use Laravel\Spark\Spark;

class SubscribeTeamUsingMollie implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function handle($team, $plan, $fromRegistration, array $data)
    {
        // We need to check if this application is storing billing addresses and if so
        // we will update the billing address in the database so that any tax information
        // on the user will be up to date via the taxPercentage method on the billable.
        if (Spark::collectsBillingAddress()) {
            Spark::call(
                TeamRepository::class.'@updateBillingAddress',
                [$team, $data]
            );
        }

        // If this application collects European VAT, we will store the VAT ID that was sent
        // with the request. It is used to determine if the VAT should get charged at all
        // when billing the customer. When it is present, VAT is not typically charged.
        if (Spark::collectsEuropeanVat()) {
            Spark::call(
                TeamRepository::class.'@updateVatId',
                [$team, Arr::get($data, 'vat_id')]
            );
        }

        $subscription = $this->getSubscriptionBuilder($team, $plan, $fromRegistration, $data);

        // Here we will check if we need to skip trial or set trial days on the subscription
        // when creating it on the provider. By default, we will skip the trial when this
        // interaction isn't from registration since they have already usually trialed.
        if (! $fromRegistration && $team->hasEverSubscribedTo('default', $plan->id)) {
            $subscription->skipTrial();
        } elseif ($plan->trialDays > 0) {
            $subscription->trialDays($plan->trialDays);
        }

        if (isset($data['coupon'])) {
            $subscription->withCoupon($data['coupon']);
        }

        if (Spark::chargesTeamsPerMember() || Spark::chargesTeamsPerSeat()) {
            $subscription->quantity(Spark::teamSeatsCount($team));
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
            $payment->metadata->fromRegistration = $fromRegistration;
            $payment->update();

            return response([
                'subscribeViaCheckout' => true,
                'checkoutUrl' => $response->getTargetUrl(),
            ]);
        }

        event(new TeamSubscribed(
            $team = $team->fresh(), $plan
        ));

        return $team;
    }

    /**
     * Get the appropriate SubscriptionBuilder for the scenario: go through Mollie's checkout or not.
     *
     * @param $billable
     * @param $plan
     * @param $fromRegistration
     * @param array $data
     * @return \Laravel\Cashier\SubscriptionBuilder\Contracts\SubscriptionBuilder
     */
    protected function getSubscriptionBuilder($billable, $plan, $fromRegistration, array $data)
    {
        if(isset($data['use_existing_payment_method'])) {

            return $data['use_existing_payment_method']
                ? $billable->newSubscriptionForMandateId($billable->mollieMandateId(), 'default', $plan->id)
                : $billable->newSubscriptionViaMollieCheckout(
                    'default', $plan->id, $this->getFirstPaymentOptions($billable)
                );
        }

        // Let Cashier decide whether to let the customer go through checkout
        return $billable->newSubscription('default', $plan->id, $this->getFirstPaymentOptions($billable));
    }

    /**
     * @param $billable
     * @return array
     */
    protected function getFirstPaymentOptions($billable)
    {
        return [
            'restrictPaymentMethodsToCountry' => Spark::collectsEuropeanVat() ? $billable->billing_country : NULL,
        ];
    }
}
