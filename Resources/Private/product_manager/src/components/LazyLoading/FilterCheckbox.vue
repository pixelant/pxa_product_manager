<template>
    <div id="filterCheckbox">
        <div class="checkbox-filter-wrapper" :class="accordionClasses">
            <div class="checkbox-filter-header" @click="toggleAccordion">
                <span class="placeholder">{{placeholder}}</span>
            </div>
            <div class="checkbox-filter-body">
                <div class="checkbox-filter-content">
                    <div v-for="option in options" v-bind:key="option.value">
                        <input class="checkbox-filter-check" type="checkbox" :value="option" @change="emitUpdate" v-model="value" />
                        <label class="checkbox-filter-label" for="label" :options="option.label" v-text="option.label"></label><br>
                    </div>
                    <button class="btn btn-clear" @click="clearChecked"> CLEAR </button>
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
                isOpen: true
            }
        },

        computed: {
            placeholder() {
                return this.filter.label || this.$options.filters.trans('please_select');
            },
            accordionClasses() {
                return {
                    'is-closed': !this.isOpen,
                    'is-primary': this.isOpen,
                    'is-dark': !this.isOpen
                };
            }
        },

        created() {
            EventHandler.on('filterPreSelect', filters => this.preselectOptions(filters));
            EventHandler.on('filterOptionsUpdate', options => this.updateAvailableOptions(options));
        },

        methods: {
            toggleAccordion() {
                this.isOpen = !this.isOpen;
            },
            clearChecked() {
                this.value = [];
                EventHandler.emit('filterUpdate', {
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

<style>
  .placeholder {
    text-transform: uppercase;
    font-size: 12px;
    margin: 20px 0;
    display: block;
  }

  .checkbox-filter-check {
    padding: 10px;
    display: inline-block;
    margin: 24px 15px 0 0;
  }

  .checkbox-filter-label {
    font-size: 14px;
    text-transform: uppercase;
    display: inline;
  }

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

  .btn-clear {
    background-color: rgb(122 110 102 / 0.10);
    width: 100%;
    height: 31px;
    padding: 0;
    text-align: center;
    margin-bottom: 20px;
    margin-top: 20px;
  }

</style>