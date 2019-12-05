<spark-update-payment-method-mollie :user="user" :team="team" :billable-type="billableType" inline-template>
    <div class="card card-default">
        <!-- Update Payment Method Heading -->
        <div class="card-header">
            <div class="float-left">
                {{__('Update Payment Method')}}
            </div>

            <div class="float-right">
                <span v-if="billable.card_last_four">
                    <i :class="['fa', 'fa-btn', cardIcon]"></i>
                    ************@{{ billable.card_last_four }}
                </span>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="card-body">

            <!-- Generic 500 Level Error Message / Mollie Threw Exception -->
            <div class="alert alert-danger" v-if="form.errors.has('form')">
                {{__("We had trouble updating your payment method. It's possible your payment provider is preventing us from charging the payment method. Please contact your payment provider or customer support.")}}
            </div>

            <form role="form">

                <!-- Update Payment Method Description -->
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{__('New card')}}</label>

                    <div class="col-md-6">
                        <label class="col-form-label">
                            {{__('For security reasons your new card will be charged a minimal fee upon registration.')}}
                         </label>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" @click.prevent="update" :disabled="form.busy">
                            <span v-if="form.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i> {{__('Redirecting')}}
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
</spark-update-payment-method-mollie>
