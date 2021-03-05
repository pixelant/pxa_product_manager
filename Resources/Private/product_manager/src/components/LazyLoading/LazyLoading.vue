<script>
    import Loader from "./Loader";
    import LazyLoadingRequest from "../../api/LazyLoadingRequest";
    import Demand from "../../model/Demand";
    import EventHandler from "../../event/EventHandler";
    import queryString from 'query-string';

    const queryStringOptions = {
        arrayFormat: 'comma',
        encode: false
    };

    export default {
        name: "LazyLoading",

        props: {
            listEndpoint: {
                type: String,
                required: true,
            },
            filterEndpoint: {
                type: String,
                required: true,
            },
            settings: {
                type: Object,
                required: true,
            },
        },

        components: {
            Loader
        },

        data() {
            const querySettings = this.parseSettingsFromHash();

            return {
                demand: new Demand(querySettings || this.settings),
                request: new LazyLoadingRequest(this.listEndpoint, this.filterEndpoint),
                initialOffSet: parseInt(querySettings ? querySettings.offSet : 0),

                loading: true,
                nextQueueLoading: false,
                products: [],
                countAll: 0,
            }
        },

        computed: {
            hasMore() {
                return this.settings.limit > 0 && (this.products.length < this.countAll);
            },

            countAllLabel() {
                return this.countAll ? this.countAll : '--';
            },

            loadMoreText() {
                const key = this.nextQueueLoading ? 'loading' : 'load_more';
                return this.$options.filters.trans(key);
            }
        },

        created() {
            this.initLoad();

            EventHandler.on('filterUpdate', data => {
                this.demand.updateFilter(data.filter, data.options);

                // Reset offset
                this.demand.offSet = 0;
                this.initLoad();

                // With activated filters update query string
                this.updateQueryString();
            })

            EventHandler.on('filterUpdateDemand', data => {
                this.demand.updateFilter(data.filter, data.options);
            })

            EventHandler.on('filtersCleared', () => {
                // Reset offset
                this.demand.offSet = 0;
                this.initLoad();

                // With activated filters update query string
                this.updateQueryString();
            })

            EventHandler.on('sortingUpdate', data => {
                this.demand.updateOrderby(data.orderBy, data.orderDirection)
                // Reset offset
                this.demand.offSet = 0;
                this.initLoad();

                // With activated filters update query string
                this.updateQueryString();
            });

        },

        methods: {
            parseSettingsFromHash() {
                const hash = window.location.hash;
                if (hash !== '') {
                    let settings = queryString.parse(hash, queryStringOptions);

                    if (typeof settings.filters === 'undefined'){
                        return null
                    }

                    settings.filters = JSON.parse(settings.filters);

                    settings.storagePid = JSON.parse(settings.storagePid);

                    // Emit preselect even, so filters will read data
                    EventHandler.emit('filterPreSelect', settings.filters);

                    EventHandler.emit('sortingPreSelect', [settings.orderBy, settings.orderDirection]);

                    return settings;
                }

                return null;
            },

            /**
             * First load
             */
            initLoad() {
                this.loading = true;
                this.countAll = 0;

                let demand = Object.assign({}, this.demand);
                demand.offSet = 0;

                if (this.settings.limit && this.initialOffSet) {
                    demand.limit += this.initialOffSet;
                    this.initialOffSet = 0;
                }

                // Load available options
                const availableOptionsRequest = this.request.loadAvailableOptions(demand);

                // Only load products
                EventHandler.emit('totalCountUpdated', '');

                this.request.loadProducts(demand)
                    .then(({data}) => {
                        this.products = data.products;
                        this.loading = false;

                        this.updateAvailableOptions(availableOptionsRequest);

                    })
                    .catch(error => console.error('Error while request products:', error));
            },

            /**
             * Update count all and fire even
             * with available filter options
             */
            updateAvailableOptions(request) {
                request
                    .then(({data}) => {
                        this.countAll = data.countAll;

                        EventHandler.emit('filterOptionsUpdate', data.options);
                        EventHandler.emit('totalCountUpdated', data.countAll);

                    })
                    .catch(error => console.error('Error while request filter options:', error));
            },

            /**
             * On load more click
             */
            loadMore() {
                this.nextQueueLoading = true;
                this.demand.offSet += this.settings.limit;

                this.request.loadProducts(this.demand)
                    .then(({data}) => {
                        this.products = this.products.concat(data.products);

                        this.updateQueryString();
                        this.nextQueueLoading = false;
                    })
                    .catch(error => console.error('Error while request products:', error));
            },

            /**
             * Set demand state to query string
             */
            updateQueryString() {
                let hash = '-';

                if (this.demand.hasQueryStringChanges()) {
                    hash = queryString.stringify(
                        this.demand.asQueryParams(),
                        queryStringOptions
                    );
                }

                window.location.hash = hash;
            }
        },
    }
</script>

<style>
    .lazy-loading-wrapper {
        position: relative;
        margin-top: 30px;
    }

    .lazy-loading-wrapper.loading {
        min-height: 100px;
    }
</style>
