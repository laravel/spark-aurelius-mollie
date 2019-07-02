@if (Spark::billsUsingBraintree())
    @include('spark::settings.subscription.subscribe-braintree')
@elseif (Spark::billsUsingMollie())
    @include('spark::settings.subscription.subscribe-mollie')
@else
    @include('spark::settings.subscription.subscribe-stripe')
@endif
