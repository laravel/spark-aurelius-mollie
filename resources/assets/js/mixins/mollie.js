module.exports = {
    data() {
        return {
            paymentStatus: null,
        };
    },

    methods: {

        /**
         * Retrieve a GET encoded query argument from the URL and remove it.
         *
         * @param key
         * @param fallback
         * @returns {*}
         */
        fetchAndRemoveFromUrl(key, fallback = null) {
            let result = fallback;
            let url = new URL(window.location);
            if(url.searchParams.has(key)) {
                result = url.searchParams.get(key);
                url.searchParams.delete(key);
                window.history.replaceState({}, document.title, url.toString());
            }

            return result;
        },

        /**
         * Show a sweetAlert modal.
         *
         * @param title
         * @param text
         * @param type
         */
        sweetAlert(title, text, type) {
            swal({
                title: __(title),
                text: __(text),
                type: type,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    },
};
