<?php

namespace Laravel\Spark\Interactions\Settings\PaymentMethod;

use Laravel\Cashier\FirstPayment\Actions\AddGenericOrderItem;
use Laravel\Cashier\FirstPayment\FirstPaymentBuilder;
use Laravel\Spark\User;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Contracts\Repositories\TeamRepository;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod;

class UpdateMolliePaymentMethod implements UpdatePaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function handle($billable, array $data)
    {
        $addOrderItem = new AddGenericOrderItem(
            $billable,
            mollie_array_to_money(config('cashier.first_payment.amount')),
            config('cashier.first_payment.description')
        );

        $payment = (new FirstPaymentBuilder($billable))
            ->setRedirectUrl('/settings#/payment-method')
            ->inOrderTo([$addOrderItem])
            ->create();

        return response([
            'data' => [
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ],
        ]);
    }

    /**
     * Get the repository class name for a given billable instance.
     *
     * @param  mixed  $billable
     * @return string
     */
    protected function updateBillingAddressMethod($billable)
    {
        return ($billable instanceof User
                    ? UserRepository::class
                    : TeamRepository::class).'@updateBillingAddress';
    }
}
