<?php

namespace Laravel\Spark\Interactions\Settings\PaymentMethod;

use Laravel\Cashier\FirstPayment\Actions\AddBalance;
use Laravel\Cashier\FirstPayment\Actions\AddGenericOrderItem;
use Laravel\Cashier\FirstPayment\FirstPaymentBuilder;
use Laravel\Spark\Spark;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod;
use Money\Money;

class UpdateMolliePaymentMethod implements UpdatePaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function handle($billable, array $data)
    {
        $payment = (new FirstPaymentBuilder($billable, $this->getFirstPaymentOptions($billable)))
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
     * @param $billable
     * @return array
     */
    protected function getFirstPaymentOptions($billable)
    {
        return [
            'restrictPaymentMethodsToCountry' => Spark::collectsEuropeanVat() ? $billable->billing_country : NULL,
        ];
    }

    /**
     * @param mixed $billable
     * @param array $data
     * @return array
     */
    protected function getPaymentActions($billable, $data)
    {
        if(Spark::addingPaymentFeeToBalance()) {
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
