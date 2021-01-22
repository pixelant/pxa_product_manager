<template>
    <div id="filterCheckbox">
        <div class="checkbox-filter-wrapper" :class="accordionClasses">
            <div class="checkbox-filter-header" @click="toggleAccordion">
                <span class="toggle-icon"></span><span class="filter-name">{{ placeholder }}</span>
            </div>
            <div class="checkbox-filter-body">
                <div class="checkbox-filter-content">
                    <div v-for="option in options" v-bind:key="option.value">
                        <input class="checkbox-filter-check" :id="option.value + option.label" type="checkbox" :value="option" @change="emitUpdate" v-model="value" />
                        <label class="checkbox-filter-label" :for="option.value + option.label" :options="option.label" v-text="option.label"></label><br>
                    </div>
                    <button class="btn-clear" @click="clearChecked"> {{ 'clear' | trans }} </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import EventHandler from "../../event/EventHandler";

    export default {
        name: "FilterCheckbox",

        props: {
            filter: Object,
        },

        data() {
            return {
                value: [],
                options: this.filter.options,
                isOpen: null
            }
        },

        computed: {
            placeholder() {
                return this.filter.label || this.$options.filters.trans('please_select');
            },
            accordionClasses() {
                return {
                    'is-closed': !this.isOpen,
                    'is-open': this.isOpen,
                    'is-static': this.filter.gui_state == 'plain'
                };
            }
        },

        created() {
            EventHandler.on('filterPreSelect', filters => this.preselectOptions(filters));
            EventHandler.on('filterOptionsUpdate', options => this.updateAvailableOptions(options));
            EventHandler.on('clear-all', () => this.clearAllChecked())
            this.checkAccordionCollapsed();
        },

        methods: {
            toggleAccordion() {
                if (this.filter.gui_state !== 'plain'){
                    this.isOpen = !this.isOpen;
                }
            },
            checkAccordionCollapsed() {
                if (this.filter.gui_state == 'collapsed') {
                    this.isOpen = false;
                } else {
                    this.isOpen = true;
                }
            },
            clearChecked() {
                this.value = [];
                EventHandler.emit('filterUpdate', {
                    filter: this.filter,
                    options: this.value,
                });
            },
            clearAllChecked() {
                this.value = [];
                EventHandler.emit('filterUpdateDemand', {
                    filter: this.filter,
                    options: this.value,
                });
            },
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

<style scoped>
  .checkbox-filter-header {
    cursor: pointer;
  }
  .checkbox-filter-body {
    padding: 0;
    max-height: 1000px;
    overflow: hidden;
    transition: 0.3s ease all;
  }
  .is-closed .checkbox-filter-body {
    max-height: 0;
  }
  .checkbox-filter-wrapper.is-closed .toggle-icon:before {
      content: "+";
  }
  .checkbox-filter-wrapper.is-open .toggle-icon:before {
      content: "-";
  }
  .checkbox-filter-wrapper.is-open.is-static .toggle-icon:before {
      content: "";
  }
</style>
