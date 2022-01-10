export default {
    install: (app) => {
        function page() {
            return app.config.globalProperties.$page;
        }

        function hasPriceAdded() {
            return !!page().props.auth.user.settingsData.price_added;
        }

        function hasCardCondition() {
            return !!page().props.auth.user.settingsData.card_condition;
        }

        function hasSettings() {
            return hasPriceAdded() || hasCardCondition();
        }

        app.config.globalProperties.$settings = {
            hasPriceAdded,
            hasCardCondition,
            hasSettings,
        };
    },
};
