module.exports = {
    props: ['user', 'team', 'plans', 'billableType'],

    /**
     * Load mixins for the component.
     */
    mixins: [
        require('./../../mixins/plans'),
        require('./../../mixins/subscriptions'),
        require('./../../mixins/vat')
    ],


    /**
     * The component's data.
     */
    data() {
        return {
            taxRate: 0,

            cardElement: null,

            form: new SparkForm({
                use_existing_payment_method: this.hasPaymentMethod() ? '1' : '0',
                plan: '',
                coupon: null,
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: Spark.defaultBillableCountry,
                vat_id: ''
            }),

            cardForm: new SparkForm({
                name: ''
            }),
        };
    },


    watch: {
        /**
         * Watch for changes on the entire billing address.
         */
        'currentBillingAddress': function (value) {
            if ( ! Spark.collectsEuropeanVat) {
                return;
            }

            this.refreshTaxRate(this.form);
        }
    },


    /**
     * Prepare the component.
     */
    mounted() {
        this.initializeBillingAddress();

        if (this.onlyHasYearlyPaidPlans) {
            this.showYearlyPlans();
        }
    },


    methods: {
        /**
         * Initialize the billing address form for the billable entity.
         */
        initializeBillingAddress() {
            this.form.address = this.billable.billing_address;
            this.form.address_line_2 = this.billable.billing_address_line_2;
            this.form.city = this.billable.billing_city;
            this.form.state = this.billable.billing_state;
            this.form.zip = this.billable.billing_zip;
            this.form.country = this.billable.billing_country || Spark.defaultBillableCountry;
            this.form.vat_id = this.billable.vat_id;
        },


        /**
         * Mark the given plan as selected.
         */
        selectPlan(plan) {
            this.selectedPlan = plan;

            this.form.plan = this.selectedPlan.id;
        },


        /**
         * Subscribe to the specified plan.
         */
        subscribe() {
            this.cardForm.errors.forget();

            this.form.startProcessing();

            this.createSubscription();
        },


        /*
         * Create subscription on the Spark server.
         */
        createSubscription() {

            Spark.post(this.urlForNewSubscription, this.form)
                .then((data) => {
                    if(data && data.subscribeViaCheckout) {
                        this.form.busy = true; // Remain busy until returned from checkout.
                        window.location.replace(data.checkoutUrl);
                    } else {
                        Bus.$emit('updateUser');
                        Bus.$emit('updateTeam');
                    }
                });
        },


        /**
         * Determine if the user has subscribed to the given plan before.
         */
        hasSubscribed(plan) {
            return !!_.filter(this.billable.subscriptions, {provider_plan: plan.id}).length
        },


        /**
         * Show the plan details for the given plan.
         *
         * We'll ask the parent subscription component to display it.
         */
        showPlanDetails(plan) {
            this.$parent.$emit('showPlanDetails', plan);
        },


        /**
         * Determine if the user/team has a payment method defined.
         */
        hasPaymentMethod() {
            return this.team ? this.team.valid_mollie_mandate : this.user.valid_mollie_mandate;
        }
    },


    computed: {
        /**
         * Get the billable entity's "billable" name.
         */
        billableName() {
            return this.billingUser ? this.user.name : this.team.owner.name;
        },


        /**
         * Determine if the selected country collects European VAT.
         */
        countryCollectsVat()  {
            return this.collectsVat(this.form.country);
        },


        /**
         * Get the URL for subscribing to a plan.
         */
        urlForNewSubscription() {
            return this.billingUser
                            ? '/settings/subscription'
                            : `/settings/${Spark.teamsPrefix}/${this.team.id}/subscription`;
        },


        /**
         * Get the current billing address from the subscribe form.
         *
         * This used primarily for watching.
         */
        currentBillingAddress() {
            return this.form.address +
                   this.form.address_line_2 +
                   this.form.city +
                   this.form.state +
                   this.form.zip +
                   this.form.country +
                   this.form.vat_id;
        },

        /**
         * Determine if the customer will be redirected to Mollie's checkout in order to subscribe.
         */
        willRedirect() {
            const validMandate = this.user.valid_mollie_mandate;
            const pleaseReuse = "1" === this.form.use_existing_payment_method;

            return !validMandate || (validMandate && !pleaseReuse);
        },
    }
};
