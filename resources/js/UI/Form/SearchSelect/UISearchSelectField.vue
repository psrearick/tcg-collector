<template>
    <div>
        <button
            type="button"
            class="
                relative
                w-full
                bg-white
                border border-gray-300
                rounded-md
                shadow-sm
                pl-3
                pr-10
                text-left
                cursor-default
                focus:outline-none
                focus:ring-1 focus:ring-indigo-500
                focus:border-indigo-500
                sm:text-sm
                z-10
                md:h-10
            "
            aria-haspopup="listbox"
            aria-expanded="true"
            aria-labelledby="listbox-label"
            @click="$emit('update-active', true)"
        >
            <span class="w-full inline-flex truncate">
                <input
                    v-if="active"
                    ref="input"
                    type="text"
                    class="
                        w-full
                        border-0
                        outline-none
                        ring-0
                        focus:outline-none
                        focus:ring-0
                        text-sm
                    "
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                />
                <span v-else class="py-2 w-full inline-flex truncate">
                    <span class="truncate">
                        {{ selectedOption.primary }}
                    </span>
                    <span class="ml-2 truncate text-gray-500">
                        {{ selectedOption.secondary }}
                    </span>
                </span>
            </span>
            <span
                class="
                    absolute
                    inset-y-0
                    right-0
                    flex
                    items-center
                    pr-2
                    pointer-events-none
                "
            >
                <ui-icon icon="selector" />
            </span>
        </button>
        <button
            v-if="active"
            class="
                fixed
                z-15
                top-0
                left-0
                h-full
                w-full
                bg-gray-500 bg-opacity-25
            "
            @click="$emit('update-active', false)"
        />
    </div>
</template>

<script>
import UiIcon from "@/UI/UIIcon";

export default {
    name: "UiSearchSelectField",

    components: { UiIcon },

    props: {
        active: {
            type: Boolean,
            default: false,
        },
        selectedOption: {
            type: Object,
            default: () => {},
        },
        modelValue: {
            type: String,
            default: "",
        },
    },

    emits: ["update-active", "update:modelValue"],

    watch: {
        active(val) {
            if (val) {
                this.$nextTick(() => {
                    this.$refs["input"].focus();
                });
            }
        },
    },
};
</script>
