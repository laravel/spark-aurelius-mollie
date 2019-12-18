module.exports = {
    props: ['user', 'team', 'billableType'],

    /**
     * Load mixins for the component.
     */
    mixins: [
        require('../../mixins/mollie')
    ],

    /**
     * The component's data.
     */
    data() {
        return {
            cardElement: null,

            form: new SparkForm({
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: Spark.defaultBillableCountry,
            }),

            paymentStatus: null,
        };
    },


    /**
     * Prepare the component.
     */
    mounted() {
        this.showPaymentStatusModal();
    },

    methods: {
        /**
         * Show the payment status modal if the customer is returning from Mollie's checkout.
         */
        showPaymentStatusModal() {
            this.paymentStatus = this.fetchAndRemoveFromUrl('payment-method-status');

            if(this.paymentStatus === 'paid') {
                this.sweetAlert(__('Got It!'), __('Your payment method has been updated.'), 'success');
            } else if(['failed', 'expired'].includes(this.paymentStatus)) {
                this.sweetAlert(__('Oh no!'), __('Your payment method could not be updated.'), 'warning');
            }
        },

        /**
         * Update the billable's payment method via Mollie's checkout.
         */
        update() {
            this.form.busy = true;
            this.form.errors.forget();
            this.form.successful = false;

            Spark.put(this.urlForUpdate, this.form)
                .then(({data}) => {
                    this.form.busy = true; // Remain busy until returned from checkout.
                    window.location.replace(data.checkoutUrl);
                });
        },
    },


    computed: {
        /**
         * Get the URL for the payment method update.
         */
        urlForUpdate() {
            return this.billingUser
                ? '/settings/payment-method'
                : `/settings/${Spark.teamsPrefix}/${this.team.id}/payment-method`;
        },


        /**
         * Get the proper brand icon for the customer's credit card.
         */
        cardIcon() {
            if (! this.billable.card_brand) {
                return 'fa-credit-card';
            }

            switch (this.billable.card_brand) {
                case 'American Express':
                    return 'fa-cc-amex';
                case 'Diners Club':
                    return 'fa-cc-diners-club';
                case 'Discover':
                    return 'fa-cc-discover';
                case 'JCB':
                    return 'fa-cc-jcb';
                case 'Mastercard':
                    return 'fa-cc-mastercard';
                case 'Visa':
                    return 'fa-cc-visa';
                default:
                    return 'fa-credit-card';
            }
        },

        /**
         * Get the billable's registered billing country.
         *
         * @returns null|string
         */
        billingCountry() {
            return this.billingUser
                ? this.user.billing_country
                : this.team.billing_country;
        },

        /**
         * If collecting European VAT, the billing country should be registered prior to setting the payment
         * method.
         */
        vatGuardOk() {
            return Spark.collectsEuropeanVat && !!this.billingCountry;
        },

        message() {
            return this.vatGuardOk
                ? __("For security reasons your new card will be charged a minimal fee upon registration.")
                : this.billingUser
                    ? __("Please first register your billing address.")
                    : __("Please first register your team's billing address.");
        },

        /**
         * Get the form disabled state.
         *
         * @returns {boolean}
         */
        disabled() {
            return this.form.busy || !this.vatGuardOk;
        },

        /**
         * Get the placeholder for the billable entity's credit card.
         */
        placeholder() {
            if (this.billable.card_last_four) {
                return `************${this.billable.card_last_four}`;
            }

            return '';
        }
    }
};
