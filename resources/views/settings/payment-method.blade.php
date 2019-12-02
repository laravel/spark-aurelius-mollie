@if (Spark::billsUsingMollie())
    @include('spark::settings.payment-method-mollie')
@else
    @include('spark::settings.payment-method-stripe')
@endif
