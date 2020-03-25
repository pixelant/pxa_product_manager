import Vue from 'vue';

/**
 * Singleton instance
 */
let _instance;

/**
 * Events helper
 */
class EventHandler
{
    /**
     * Initialize
     */
    constructor() {
        if (_instance === null) {
            _instance = this;
        }

        this.vue = new Vue();

        return _instance;
    }

    /**
     * Fire event
     *
     * @param event
     * @param data
     */
    emit(event, data = null) {
        this.vue.$emit(event, data);
    }

    /**
     * Listen custom event
     *
     * @param event
     * @param callback
     */
    on(event, callback) {
        this.vue.$on(event, callback);
    }
}

export default new EventHandler();
