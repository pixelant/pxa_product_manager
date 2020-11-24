import axios from 'axios';
import {objectToFormData} from 'object-to-formdata';

/**
 * Request lazy loading endpoint
 */
class LazyLoadingRequest
{
    /**
     * Construct with URL
     *
     * @param listUrl
     * @param filterUrl
     */
    constructor(listUrl, filterUrl) {
        this.listUrl = listUrl;
        this.filterUrl = filterUrl;
    }

    /**
     * Load products with demand
     *
     * @param demand
     * @returns {*}
     */
    loadProducts(demand) {
        return axios.post(
            this.listUrl,
            objectToFormData({
                tx_pxaproductmanager_lazyloading: {
                    demand: demand
                }
            }) // Extbase doesn't understand json
        )
    }

    /**
     * Request to count all products and load available options
     *
     * @param demand
     */
    loadAvailableOptions(demand) {
        return axios.post(
            this.filterUrl,
            objectToFormData({
                tx_pxaproductmanager_lazyavailablefilters: {
                    demand: demand
                }
            }) // Extbase doesn't understand json
        )
    }
}

export default LazyLoadingRequest;
