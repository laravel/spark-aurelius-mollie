<?php

namespace App\Models;

use Laravel\Spark\Subscription;
use Laravel\Spark\Team as SparkTeam;

class Team extends SparkTeam
{
    /**
     * Get all of the subscriptions for the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'owner')->orderBy('created_at', 'desc');
    }

    /**
     * Get the receiver information for the invoice.
     * Typically includes the name and some sort of (E-mail/physical) address.
     *
     * @return array An array of strings
     */
    public function getInvoiceInformation()
    {
        $owner = $this->owner;

        return array_filter([
            $owner->name,
            $this->name,
            $this->billing_address,
            $this->billing_address_line_2,
            $this->billing_city,
            $this->billing_zip,
            $this->billing_country,
            $owner->email,
        ]);
    }

    /**
     * Get additional information to be displayed on the invoice.
     * Typically a note provided by the customer.
     *
     * @return string|null
     */
    public function getExtraBillingInformation()
    {
        return $this->extra_billing_information;
    }
}
