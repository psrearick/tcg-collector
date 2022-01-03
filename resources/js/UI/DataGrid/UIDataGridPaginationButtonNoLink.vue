<template>
    <div :class="classes">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "UiDataGridPaginationButtonNoLink",

    props: {
        pageIndex: {
            type: Number,
            default: 0,
        },
        active: {
            type: Boolean,
            default: false,
        },
        classAdditions: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            pageNumber: 0,
        };
    },
    computed: {
        classes() {
            return (
                "relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-pointer" +
                (this.classAdditions ? " " + this.classAdditions + " " : "") +
                (this.active
                    ? " bg-indigo-50 border-indigo-500 text-indigo-600 z-20"
                    : " bg-white border-gray-300 text-gray-500 hover:bg-gray-50 z-10")
            );
        },
    },
};
</script>
