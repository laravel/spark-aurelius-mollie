module.exports = {
    props: ['user', 'team', 'plans', 'billableType'],

    /**
     * Load mixins for the component.
     */
    mixins: [
        require('./../../mixins/mollie'),
    ],

    /**
     * The component's data.
     */
    data() {
        return {
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
            this.paymentStatus = this.fetchAndRemoveFromUrl('new-subscription-status');

            if(this.paymentStatus === 'paid') {
                this.sweetAlert(__('Got It!'), __('Welcome to your new subscription.'), 'success');
            } else if(['failed', 'expired'].includes(this.paymentStatus)) {
                this.sweetAlert(__('Oh no!'), __('Your payment went wrong. Try again or contact support'), 'warning');
            }
        },

    },
};
