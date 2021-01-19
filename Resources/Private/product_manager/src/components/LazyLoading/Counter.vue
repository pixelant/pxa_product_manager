<template>
    <div id="counter">
        <span class="counter" v-cloak>{{ 'total_results' | trans }} {{ countAllLabel }}</span>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "Counter",

        data() {
            return {
                countAll: 0,
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
        },

    }
</script>
