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
            return {
                loading: true,
                products: [],

                demand: new Demand(this.parseSettingsFromHash() || this.settings),
                request: new LazyLoadingRequest(this.endpoint),
            }
        },

        created() {
            this.loadProducts();

            EventHandler.on('filterUpdate', data => {
                this.demand.updateFilter(data.filter, data.options);
                this.loadProducts();

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

                    return settings;
                }

                return null;
            },

            loadProducts() {
                this.loading = true;

                this.request.load(this.demand)
                    .then(({data}) => {
                        this.products = data.products;

                        this.loading = false;
                    })
                    .catch(error => console.error('Error while request products:', error));
            },

            updateQueryString() {
                let hash = '';

                if (this.demand.hasFilters()) {
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