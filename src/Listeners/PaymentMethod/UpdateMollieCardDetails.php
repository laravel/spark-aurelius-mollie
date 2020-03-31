<?php

namespace Laravel\Spark\Listeners\PaymentMethod;

use Laravel\Cashier\Events\MandateUpdated;
use Laravel\Spark\Events\PaymentMethod\PaymentMethodUpdated;
use Mollie\Api\Types\MandateMethod;

class UpdateMollieCardDetails
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $billable;

    /** @var \Mollie\Api\Resources\Mandate */
    protected $mandate;

    /** @var \Mollie\Api\Resources\Payment */
    protected $payment;

    /** @var string */
    protected $cardCountry;

    /** @var string */
    protected $cardBrand;

    /** @var string */
    protected $cardNumber;

    /**
     * Handle the event.
     *
     * @param \Laravel\Cashier\Events\MandateUpdated $event
     * @return void
     */
    public function handle(MandateUpdated $event)
    {
        $this->payment = $event->payment;
        $this->billable = $event->owner;
        $this->mandate = $this->billable->mollieMandate();

        if ($this->mandate->method === MandateMethod::CREDITCARD) {
            $this->handleCreditCard();
        } elseif ($this->mandate->method === MandateMethod::DIRECTDEBIT) {
            $this->handleDirectDebit();
        }

        $this->billable->forceFill([
            'card_country' => $this->cardCountry,
            'card_brand' => $this->cardBrand,
            'card_last_four' => $this->cardNumber,
        ])->save();

        event(new PaymentMethodUpdated($this->billable));
    }

    /**
     * Extract credit card details.
     */
    protected function handleCreditCard()
    {
        $this->cardCountry = $this->payment->details->cardCountryCode;
        $this->cardBrand = $this->payment->details->cardLabel;
        $this->cardNumber = (string) $this->payment->details->cardNumber;
    }

    /**
     * Extract debit card details
     */
    protected function handleDirectDebit()
    {
        $details = $this->mandate->details;
        $consumerAccount = $details->consumerAccount;

        $this->cardCountry = strtoupper((string) substr($consumerAccount, 0, 2));
        $this->cardBrand = 'BIC_' . $details->consumerBic;
        $this->cardNumber = (string) substr($consumerAccount, -4);
    }
}
