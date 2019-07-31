module.exports = {
    props: ['user', 'team', 'billableType'],

    /**
     * Load mixins for the component.
     */
    mixins: [],

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
                country: 'US'
            }),
        };
    },


    /**
     * Prepare the component.
     */
    mounted() {
        //
    },


    methods: {
        /**
         * Update the billable's card information.
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
