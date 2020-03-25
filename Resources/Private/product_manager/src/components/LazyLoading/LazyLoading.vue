<script>
    import Loader from "./Loader";
    import LazyLoadingRequest from "../../api/LazyLoadingRequest";
    import Demand from "../../model/Demand";
    import EventHandler from "../../event/EventHandler";

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

                demand: new Demand(this.settings),
                request: new LazyLoadingRequest(this.endpoint),
            }
        },

        created() {
            this.loadProducts();

            EventHandler.on('filterUpdate', data => {
                this.demand.updateFilter(data.filter, data.options);
                this.loadProducts();
            })
        },

        methods: {
            loadProducts() {
                this.request.load(this.demand)
                    .then(({data}) => {
                        this.products = data.products;

                        this.loading = false;
                    })
                    .catch(error => console.error('Error while request products:', error));
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