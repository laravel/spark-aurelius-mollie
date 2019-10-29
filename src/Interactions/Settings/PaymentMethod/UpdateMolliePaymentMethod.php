<?php

namespace Laravel\Spark\Interactions\Settings\PaymentMethod;

use Laravel\Cashier\FirstPayment\Actions\AddGenericOrderItem;
use Laravel\Cashier\FirstPayment\FirstPaymentBuilder;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod;
use Money\Money;

class UpdateMolliePaymentMethod implements UpdatePaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function handle($billable, array $data)
    {
        $subtotal = $this->subtotalForTotalIncludingTax(
            mollie_array_to_money(config('cashier.first_payment.amount')),
            $billable->taxPercentage() * 0.01
        );

        $addOrderItem = new AddGenericOrderItem($billable, $subtotal, __("Payment method updated"));

        $payment = (new FirstPaymentBuilder($billable))
            ->setRedirectUrl('/settings#/payment-method')
            ->inOrderTo([$addOrderItem])
            ->create();

        $payment->redirectUrl = url('/redirects/mollie/update-payment-method/' . $payment->id);
        $payment->update();

        return response([
            'data' => [
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ],
        ]);
    }

    /**
     * @param \Money\Money $total
     * @param float $taxPercentage
     * @return \Money\Money
     */
    protected function subtotalForTotalIncludingTax(Money $total, float $taxPercentage)
    {
        $vat = $total->divide(1 + $taxPercentage)->multiply($taxPercentage);

        return $total->subtract($vat);
    }
}
