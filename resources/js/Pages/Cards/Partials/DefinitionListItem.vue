<template>
    <div class="grid md:grid-cols-2 px-8">
        <dt :class="border + ' py-4 font-bold'">{{ title }}</dt>
        <dd :class="border + ' py-4 text-gray-500'">
            <slot>{{ value }}</slot>
        </dd>
    </div>
</template>

<script>
export default {
    name: "DefinitionListItem",

    props: {
        title: {
            type: String,
            default: "",
        },
        value: {
            type: String,
            default: "",
        },
        border: {
            type: String,
            default: "border-t border-gray-200",
        },
    },
};
</script>
