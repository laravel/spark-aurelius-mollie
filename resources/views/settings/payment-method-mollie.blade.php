<spark-payment-method-mollie :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        <!-- Current Discount -->
        <div class="alert alert-success" v-if="currentDiscount">
            <span v-if="currentDiscount.duration=='repeating' && currentDiscount.duration_in_months > 1">@{{ __("You currently receive a discount of :discountAmount for all invoices during the next :months months.", {discountAmount: formattedDiscount(currentDiscount), months: currentDiscount.duration_in_months}) }}</span>
            <span v-if="currentDiscount.duration=='repeating' && currentDiscount.duration_in_months == 1">@{{ __("You currently receive a discount of :discountAmount for all invoices during the next month.", {discountAmount: formattedDiscount(currentDiscount)}) }}</span>
            <span v-if="currentDiscount.duration=='forever'">@{{ __("You currently receive a discount of :discountAmount forever.", {discountAmount: formattedDiscount(currentDiscount)}) }}</span>
            <span v-if="currentDiscount.duration=='once'">@{{ __("You currently receive a discount of :discountAmount for a single invoice.", {discountAmount: formattedDiscount(currentDiscount)}) }}</span>
        </div>

        <!-- Update Payment Method -->
        @include('spark::settings.payment-method.update-payment-method-mollie')

        <!-- Update Billing Address -->
        @if (Spark::collectsBillingAddress())
            @include('spark::settings.payment-method.update-billing-address-mollie')
        @endif

        <!-- Update VAT ID -->
        @if (Spark::collectsEuropeanVat())
            @include('spark::settings.payment-method.update-vat-id')
        @endif

        <div>
            <div v-if="billable.mollie_customer_id">
                <!-- Redeem Coupon -->
                @include('spark::settings.payment-method.redeem-coupon')
            </div>
        </div>
    </div>
</spark-payment-method-mollie>
