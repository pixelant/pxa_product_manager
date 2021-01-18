<template>
    <div id="sorting">
        <span>{{ 'sort_by' | trans }}</span>
        <select v-model="value" @change="sortingUpdate">
            <option v-for="option in options" v-bind:value="option.value" v-bind:key="option.value">
                {{ option.text }}
            </option>
        </select>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "Sorting",

        props: {
            options: Array,
            settings: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                value: '',
            }
        },

        created() {
            EventHandler.on('sortingPreSelect', settings => this.preselectOption(settings));
            let initSettings = [this.settings.orderBy, this.settings.orderDirection];
            this.preselectOption(initSettings);
        },

        methods: {
            sortingUpdate() {
                let values = this.value.split(',');
                let orderBy = values[0];
                let orderDirection = values[1];
                EventHandler.emit('sortingUpdate', {
                    orderBy: orderBy,
                    orderDirection: orderDirection
                });
            },

            preselectOption(settings) {
                let selectedValue = settings.join(',');
                this.value = selectedValue;
            },
        }
    }
</script>