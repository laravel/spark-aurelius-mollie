<spark-subscription :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        <div v-if="plans.length > 0">
            <!-- Trial Expiration Notice -->
            @include('spark::settings.subscription.subscription-notice')

            <!-- New Subscription -->
            <div v-if="needsSubscription">
                @include('spark::settings.subscription.subscribe')
            </div>

            <!-- Update Subscription -->
            <div v-if="subscriptionIsActive">
                @include('spark::settings.subscription.update-subscription')
            </div>

            <!-- Resume Subscription -->
            <div v-if="subscriptionIsOnGracePeriod">
                @include('spark::settings.subscription.resume-subscription')
            </div>

            <!-- Cancel Subscription -->
            <div v-if="subscriptionIsActive">
                @include('spark::settings.subscription.cancel-subscription')
            </div>
        </div>

        <!-- Plan Features Modal -->
        @include('spark::modals.plan-details')

        <!-- Subscribed Successfully Modal after completing Mollie's checkout -->
        @if(Spark::billsUsingMollie())
            @include('spark::settings.subscription.subscribed-mollie')
        @endif
    </div>
</spark-subscription>
