module.exports = {
    props: ['user', 'team', 'billableType'],

    /**
     * Load mixins for the component.
     */
    mixins: [
        //
    ],

    /**
     * The component's data.
     */
    data() {
        return {
            form: new SparkForm({
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: Spark.defaultBillableCountry
            }),
        };
    },


    /**
     * Prepare the component.
     */
    mounted() {
        this.initializeBillingAddress();
    },


    methods: {
        /**
         * Initialize the billing address form for the billable entity.
         */
        initializeBillingAddress() {
            if (! Spark.collectsBillingAddress) {
                return;
            }

            this.form.address = this.billable.billing_address;
            this.form.address_line_2 = this.billable.billing_address_line_2;
            this.form.city = this.billable.billing_city;
            this.form.state = this.billable.billing_state;
            this.form.zip = this.billable.billing_zip;
            this.form.country = this.billable.billing_country || Spark.defaultBillableCountry;
        },


        /**
         * Update the billable's card information.
         */
        update() {
            this.form.busy = true;
            this.form.errors.forget();
            this.form.successful = false;

            // Here we will build out the payload to send to Stripe to obtain a card token so
            // we can create the actual subscription. We will build out this data that has
            // this credit card number, CVC, etc. and exchange it for a secure token ID.
            // const payload = {
            //     address_line1: this.form.address || '',
            //     address_line2: this.form.address_line_2 || '',
            //     address_city: this.form.city || '',
            //     address_state: this.form.state || '',
            //     address_zip: this.form.zip || '',
            //     address_country: this.form.country || '',
            // };

            this.sendUpdateToServer();
        },


        /**
         * Send the credit card update information to the server.
         */
        sendUpdateToServer() {
            Spark.put(this.urlForUpdate, this.form)
                .then(() => {
                Bus.$emit('updateUser');
                Bus.$emit('updateTeam');
        });
        }
    },


    computed: {
        /**
         * Get the URL for the billing address update.
         */
        urlForUpdate() {
            return this.billingUser
                ? '/settings/payment-method/billing-address'
                : `/settings/${Spark.teamsPrefix}/${this.team.id}/payment-method/billing-address`;
        },
    }
};
