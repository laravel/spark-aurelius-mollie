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

            this.sendUpdateToServer();
        },


        /**
         * Send the new billing address to the server.
         */
        sendUpdateToServer() {
            Spark.put(this.urlForUpdate, this.form)
                .then(() => {
                    Bus.$emit('updateUser');
                    Bus.$emit('updateTeam');
                }
            );
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
