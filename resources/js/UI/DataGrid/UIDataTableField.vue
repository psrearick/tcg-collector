<template>
    <p
        v-if="
            (field.type === 'text' || field.type === 'array') && showField(data)
        "
        :class="data.classes"
    >
        {{ fieldValue }}
    </p>
    <p
        v-if="field.type === 'currency' && showField(data)"
        :class="data.classes"
    >
        {{ fieldValue }}
    </p>
    <p
        v-if="field.type === 'composite-text' && showField(data)"
        :class="data.classes"
    >
        <span
            v-for="(value, index) in field.values"
            v-show="data[value.key]"
            :key="index"
            :class="value.classes"
        >
            {{
                value.type === "currency"
                    ? formatCurrencyOrEmpty(data[value.key])
                    : data[value.key]
            }}
        </span>
    </p>
    <component
        :is="component"
        v-if="field.type === 'component' && showField(data)"
        :data="data"
        :field="field"
        :class="data.classes"
        v-bind="componentProps"
    />
</template>

<script>
import { formatCurrency } from "@/Shared/api/ConvertValue";
import UiHorizontalIncrementer from "@/UI/Buttons/UIHorizontalIncrementer";
import BottomRowCollectionsEdit from "@/Pages/Collections/Partials/BottomRowCollectionsEdit";
import UiButton from "@/UI/UIButton";

const componentMap = {
    HorizontalIncrementer: UiHorizontalIncrementer,
    UiButton: UiButton,
    BottomRowCollectionsEdit: BottomRowCollectionsEdit,
};

export default {
    name: "UiDataTableField",

    props: {
        field: {
            type: Object,
            default: () => {},
        },
        data: {
            type: Object,
            default: () => {},
        },
    },

    emits: ["click"],

    computed: {
        formattedValue() {
            if (typeof this.field.key === "undefined") {
                return this.field.value;
            }
            let key = this.field.key;
            let keys = key.split(".");
            let value = this.data;
            keys.forEach((key) => {
                value = value[key];
            });

            if (this.field.type === "currency") {
                return this.formatCurrencyOrEmpty(value);
            }
            return value;
        },
        fieldValue() {
            if (this.field.type === "array") {
                return this.formattedValue.join(", ");
            }
            return this.formattedValue;
        },
        componentProps() {
            return this.field.props || null;
        },
        component() {
            return this.field.component
                ? componentMap[this.field.component]
                : {};
        },
    },

    methods: {
        click() {
            this.$emit("click");
        },
        formatCurrencyOrEmpty(value) {
            if (!value) {
                return "";
            }
            value = formatCurrency(value);
            return value !== "0" ? value : "";
        },
        showField(data) {
            if (this.field.condition) {
                return this.field.condition(data);
            }
            return true;
        },
    },
};
</script>
