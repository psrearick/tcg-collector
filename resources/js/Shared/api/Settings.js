export default {
    install: (app) => {
        function expandedDefault(view) {
            if (view === "show") {
                return !!page().props.auth.user.settingsData
                    .expanded_default_show;
            }

            if (view === "edit") {
                return !!page().props.auth.user.settingsData
                    .expanded_default_edit;
            }

            return false;
        }

        function hasCardCondition() {
            return !!page().props.auth.user.settingsData.card_condition;
        }

        function hasPriceAdded() {
            return !!page().props.auth.user.settingsData.price_added;
        }

        function hasSettings() {
            return hasPriceAdded() || hasCardCondition();
        }

        function page() {
            return app.config.globalProperties.$page;
        }

        app.config.globalProperties.$settings = {
            expandedDefault,
            hasCardCondition,
            hasPriceAdded,
            hasSettings,
        };
    },
};
