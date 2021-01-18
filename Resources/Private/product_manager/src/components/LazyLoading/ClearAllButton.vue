<template>
    <div id="clearAllButton">
        <button class="btn-clear-all" @click="clearChecked">{{ 'clear_all' | trans }}</button>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "ClearAllButton",

        methods: {
            clearChecked() {
                EventHandler.emit('clear-all');
                EventHandler.emit('filtersCleared');
            },
        }
    }
</script>