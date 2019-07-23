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
        $this->billable = $event->owner;
        $this->mandate = $this->billable->mollieMandate();

        if ($this->mandate->method === MandateMethod::CREDITCARD) {
            $this->handleCreditCard();
        } elseif ($this->mandate->method === MandateMethod::DIRECTDEBIT) {
            $this->handleDirectDebit();
        }

        $this->billable->forceFill([
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
        $this->cardBrand = $this->mandate->details->cardLabel;
        $this->cardNumber = (string) $this->mandate->details->cardNumber;
    }

    /**
     * Extract debit card details
     */
    protected function handleDirectDebit()
    {
        $this->cardBrand = 'BIC_' . $this->mandate->details->consumerBic;
        $this->cardNumber = (string) substr($this->mandate->details->consumerAccount, -4);
    }
}
