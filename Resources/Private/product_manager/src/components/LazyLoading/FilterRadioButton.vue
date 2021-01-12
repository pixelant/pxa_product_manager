<template>
    <div id="filterCheckbox">
    <!--
        <div class="checkbox" v-for="option in options" v-bind:key="option.value">
            <input type="checkbox"
            v-model="value"
            :name="placeholder"
            :value="option.value"
            @input="emitUpdate"
            :id="option.value">
            <label for="label" :options="option.label" v-text="option.label"></label><br>

        </div>
        -->
            <h2>{{placeholder}}</h2>

        <div v-for="option in options" v-bind:key="option.value">
            <input type="radio" :value="option" @change="emitUpdate" v-model="value" />
            <label for="label" :options="option.label" v-text="option.label"></label><br>
        </div>

        {{checkedFilter}}


    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "FilterRadioButton",

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
            checkedFilter() {
                return this.value;
            },
            placeholder() {
                return this.filter.label || this.$options.filters.trans('please_select');
            }
        },

        created() {
            EventHandler.on('filterPreSelect', filters => this.preselectOptions(filters));
            EventHandler.on('filterOptionsUpdate', options => this.updateAvailableOptions(options));
        },

        methods: {
            

            emitUpdate(event) {
                EventHandler.emit('filterUpdate', {
                    filter: this.filter,
                    options: [this.value],
                });
                console.log(event.target.value);
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