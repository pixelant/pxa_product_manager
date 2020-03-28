<template>
    <div>
        <multiselect v-model="value"
                     :options="options"
                     :multiple="true"
                     track-by="value"
                     label="label"
                     @input="emitUpdate"
                     placeholder="Select one"></multiselect>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "LazyFilter",

        components: {
            Multiselect
        },

        props: {
            filter: Object,
        },

        data() {
            return {
                value: null,
                options: this.filter.options,
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

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
