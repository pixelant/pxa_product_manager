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

        methods: {
            emitUpdate() {
                EventHandler.emit('filterUpdate', {
                    filter: this.filter,
                    options: this.value,
                });
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
