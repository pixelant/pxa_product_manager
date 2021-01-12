<template>
    <div id="clearAllButton">
        <button> CLEAR  ALL</button>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "ClearAllButton",

        props: {
            filter: Object,
        },

        data() {
            return {
                value: [],
                options: this.filter.options,
            }
        },

        computed: {
            placeholder() {
                return this.filter.label || this.$options.filters.trans('please_select');
            }
        },

        created() {
            EventHandler.on('filterPreSelect', filters => this.preselectOptions(filters));
            EventHandler.on('filterOptionsUpdate', options => this.updateAvailableOptions(options));
        },

        methods: {
            emitUpdate() {
                EventHandler.emit('filterUpdate', {
                    filter: this.filter,
                    options: this.value,
                });
            },

            preselectOptions(filters) {
                const filterId = this.filter.uid;
                if (typeof filters[filterId] === 'undefined') {
                    return;
                }

                const preselectValue = filters[filterId].value;
                if (preselectValue) {
                    this.value = this.filter.options.filter(option => preselectValue.includes(option.value));
                }
            },

            updateAvailableOptions(options) {
                // If all is available
                if (options === null) {
                    this.options = this.filter.options;
                    return;
                }

                const available = options[this.filter.uid] || options['and'];

                this.options = this.filter.options.filter(option => available.includes(option.value));
            },

        }
    }
</script>