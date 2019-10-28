<?php

namespace Laravel\Spark\Listeners\Invoices;

use Laravel\Cashier\Events\OrderInvoiceAvailable;
use Laravel\Spark\Team;

class CreateLocalInvoice
{
    public function handle(OrderInvoiceAvailable $event)
    {
        $order = $event->order;
        $billable = $order->owner;
        $team = $billable->owner_type === Team::class;

        $billable->localInvoices()->create([
            'user_id' => $team ? null : $billable->id,
            'team_id' => $team ? $billable->id : null,
            'provider_id' => 'mollie',
            'total' => (double) money_to_decimal($order->getTotal()),
            'tax' => (double) money_to_decimal($order->getTax()),
            'card_country' => $billable->card_country,
            'billing_state' => $billable->billing_state,
            'billing_zip' => $billable->billing_zip,
            'billing_country' => $billable->billing_country,
            'vat_id' => $billable->vat_id,
        ]);
    }
}
