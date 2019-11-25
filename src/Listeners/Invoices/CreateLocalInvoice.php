<?php

namespace Laravel\Spark\Listeners\Invoices;

use Laravel\Cashier\Events\OrderInvoiceAvailable;
use Laravel\Spark\Spark;

class CreateLocalInvoice
{
    public function handle(OrderInvoiceAvailable $event)
    {
        $order = $event->order;
        $billable = $order->owner;
        $billableIsTeam = $billable->owner_type === Spark::teamModel();

        $billable->localInvoices()->create([
            'user_id' => $billableIsTeam ? null : $billable->id,
            'team_id' => $billableIsTeam ? $billable->id : null,
            'provider_id' => $order->id,
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
