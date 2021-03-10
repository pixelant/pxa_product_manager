<template>
    <div id="counter">
        <span class="counter" v-cloak>{{ 'showing' | trans }} {{ countCurrent }} {{ 'of' | trans }} {{ countAllLabel }} {{ 'products' | trans }}</span>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "Counter",

        data() {
            return {
                countAll: 0,
                countCurrent: 0,
            }
        },

        computed: {
            countAllLabel() {
                return this.countAll ? this.countAll : '--';
            },
        },

        created() {
            EventHandler.on('totalCountUpdated', countAll => {
                this.countAll = countAll
            });
            EventHandler.on('currentCountUpdated', countCurrent => {
                this.countCurrent = countCurrent
            });
        },

    }
</script>
