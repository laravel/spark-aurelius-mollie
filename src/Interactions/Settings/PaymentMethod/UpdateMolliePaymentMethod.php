<?php

namespace Laravel\Spark\Interactions\Settings\PaymentMethod;

use Laravel\Cashier\FirstPayment\Actions\AddBalance;
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
        $payment = (new FirstPaymentBuilder($billable, $this->getPaymentOptions($billable)))
            ->setRedirectUrl('/settings#/payment-method')
            ->inOrderTo($this->getPaymentActions($billable, $data))
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
     * Get payload overrides for creating the payment.
     *
     * @param $billable
     * @return array
     */
    protected function getPaymentOptions($billable)
    {
        return [
            'restrictPaymentMethodsToCountry' => $billable->billing_country,
        ];
    }

    /**
     * @param mixed $billable
     * @param array $data
     * @return array
     */
    protected function getPaymentActions($billable, $data)
    {
        if(config('spark.update-payment-method.add-to-balance', true)) {
            return $this->getAddToBalanceActions($billable, $data);
        }

        // VAT is involved if the payment is not used for adding balance
        $subtotal = $this->subtotalForTotalIncludingTax(
            mollie_array_to_money(config('cashier.first_payment.amount')),
            $billable->taxPercentage() * 0.01
        );

        return [ new AddGenericOrderItem($billable, $subtotal, __("Payment method updated")) ];
    }

    /**
     * @param mixed $billable
     * @param array $data
     * @return array
     */
    protected function getAddToBalanceActions($billable, $data)
    {
        return [
            new AddBalance(
                $billable,
                mollie_array_to_money(config('cashier.first_payment.amount')),
                __("Payment method updated")
            )
        ];
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
