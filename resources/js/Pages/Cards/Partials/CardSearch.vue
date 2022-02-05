<template>
    <div>
        <ui-checkbox
            v-model:checked="allPrintings"
            name="allPrintings"
            label="Display all printings"
            class="mb-4"
        />
        <card-set-search
            v-model="cardSearchTerm"
            v-model:set-name="setSearchTerm"
            :configure-table="true"
        />
    </div>
</template>

<script>
import UiCheckbox from "@/UI/Form/UICheckbox";
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
export default {
    name: "CardSearch",

    components: { CardSetSearch, UiCheckbox },

    data() {
        return {
            allPrintings: false,
        };
    },
};
</script>
