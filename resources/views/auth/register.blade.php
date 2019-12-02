@if (Spark::billsUsingMollie())
    @include('spark::auth.register-mollie')
@else
    @include('spark::auth.register-stripe')
@endif
