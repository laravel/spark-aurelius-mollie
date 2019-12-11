<spark-update-billing-address-mollie :user="user" :team="team" :billable-type="billableType" inline-template>
    <div class="card card-default">
        <!-- Update Billing Address Heading -->
        <div class="card-header">
            {{__('Update Billing Address')}}
        </div>

        <div class="card-body">
            <!-- Billing Address Update Success Message -->
            <div class="alert alert-success" v-if="form.successful">
                {{__('Your billing address has been updated!')}}
            </div>

            <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
            <div class="alert alert-danger" v-if="form.errors.has('form')">
                {{__('We had trouble updating your billing address. Please contact customer support.')}}
            </div>

            <form role="form">

                <!-- Billing Address Fields -->
            @if (Spark::collectsBillingAddress())
                @include('spark::settings.payment-method.update-payment-method-address')
            @endif

            <!-- Zip Code -->
                <div class="form-group row" v-if=" ! spark.collectsBillingAddress">
                    <label for="zip" class="col-md-4 col-form-label text-md-right">{{__('ZIP / Postal Code')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" v-model="form.zip">
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" @click.prevent="update" :disabled="form.busy">
                            <span v-if="form.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i> {{__('Updating')}}
                            </span>

                            <span v-else>
                                {{__('Update')}}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-billing-address-mollie>
