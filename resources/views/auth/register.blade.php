@if (Spark::billsUsingBraintree())
    @include('spark::auth.register-braintree')
@elseif(Spark::billsUsingMollie())
    @include('spark::auth.register-mollie')
@else
    @include('spark::auth.register-stripe')
@endif
