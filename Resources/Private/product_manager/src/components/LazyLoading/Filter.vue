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
            EventHandler.on('filterPreSelect', filters => {
                const filterId = this.filter.uid;
                if (typeof filters[filterId] === 'undefined') {
                    return;
                }

                const preselectValue = filters[filterId].value;
                if (preselectValue) {
                    this.value = this.findOptionsByValues(preselectValue);
                }

            });
        },

        methods: {
            emitUpdate() {
                EventHandler.emit('filterUpdate', {
                    filter: this.filter,
                    options: this.value,
                });
            },

            findOptionsByValues(values) {
                let options = [];

                for (const option in this.options) {
                    if (values.includes(this.options[option].value)) {
                        options.push(this.options[option]);
                    }
                }

                return options
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
