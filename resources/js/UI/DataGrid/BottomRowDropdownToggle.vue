<template>
    <div class="cursor-pointer" @click="expand(!showExpand)">
        <span v-if="showExpand">+</span>
        <span v-if="!showExpand">-</span>
    </div>
</template>
<script>
export default {
    name: "BottomRowDropdownToggle",

    props: {
        data: {
            type: Object,
            default: () => {},
        },
        field: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            showExpand: true,
        };
    },

    methods: {
        expand(expand) {
            this.showExpand = expand;
            this.emitter.emit("expandBottomRow", {
                expand: this.showExpand,
                field: this.field,
                data: this.data,
            });
        },
    },
};
</script>
