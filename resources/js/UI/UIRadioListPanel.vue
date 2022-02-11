<template>
    <fieldset>
        <div class="bg-white rounded-md -space-y-px">
            <label
                v-for="(option, id) in options"
                :key="id"
                :class="getOuterClass(id)"
                @click="$emit('update:value', option.key)"
            >
                <input
                    type="radio"
                    name="privacy-setting"
                    value="Public access"
                    class="
                        h-4
                        w-4
                        mt-0.5
                        cursor-pointer
                        text-primary-600
                        border-gray-300
                        focus:ring-primary-500
                    "
                    aria-labelledby="privacy-setting-0-label"
                    aria-describedby="privacy-setting-0-description"
                />
                <span class="block ml-3 flex flex-col">
                    <span
                        :class="
                            'block text-sm font-medium ' +
                            (option.key === value
                                ? 'text-primary-800'
                                : 'text-gray-900')
                        "
                    >
                        {{ option.label }}
                    </span>
                    <span
                        class="block text-sm"
                        :class="
                            'block text-sm ' +
                            (option.key === value
                                ? 'text-primary-700'
                                : 'text-gray-500')
                        "
                    >
                        {{ option.description }}
                    </span>
                </span>
            </label>
        </div>
    </fieldset>
</template>

<script>
export default {
    name: "UiRadioListPanel",

    props: {
        options: {
            type: Array,
            default: () => {},
        },
        key: {
            type: String,
            default: "key",
        },
        value: {
            type: String,
            default: "",
        },
    },

    emits: ["update:value"],

    methods: {
        getOuterClass(key) {
            let classes = `relative
                    border
                    p-4
                    flex
                    cursor-pointer
                    focus:outline-none`;

            let rounded = "";
            if (key === 0) {
                rounded = "rounded-tl-md rounded-tr-md";
            }

            if (key === this.options.length - 1) {
                rounded = "rounded-bl-md rounded-br-md";
            }

            let checked = "border-gray-200";
            if (this.options[key].key === this.value) {
                checked = "bg-indigo-50 border-indigo-200";
            }

            return `${classes} ${rounded} ${checked}`;
        },
    },
};
</script>
