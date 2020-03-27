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
            endpoint: {
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
                request: new LazyLoadingRequest(this.endpoint),
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
        },

        methods: {
            parseSettingsFromHash() {
                const hash = window.location.hash;
                if (hash !== '') {
                    let settings = queryString.parse(hash, queryStringOptions);
                    settings.filters = JSON.parse(settings.filters);

                    // Emit preselect even, so filters will read data
                    EventHandler.emit('filterPreSelect', settings.filters);

                    return settings;
                }

                return null;
            },

            /**
             * First load
             */
            initLoad() {
                this.loading = true;

                let demand = Object.assign({}, this.demand);
                demand.offSet = 0;

                if (this.settings.limit && this.initialOffSet) {
                    demand.limit += this.initialOffSet;
                    this.initialOffSet = 0;
                }

                this.request.load(demand)
                    .then(({data}) => {
                        this.products = data.products;
                        this.countAll = data.countAll;

                        EventHandler.emit('filterOptionsUpdate', data.availableFilterOptions);

                        this.loading = false;
                    })
                    .catch(error => console.error('Error while request products:', error));
            },

            /**
             * On load more click
             */
            loadMore() {
                this.nextQueueLoading = true;
                this.demand.offSet += this.settings.limit;

                this.request.load(this.demand)
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
                let hash = '';

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