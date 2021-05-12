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
        let result = axios.post(
            this.listUrl,
            objectToFormData({
                tx_pxaproductmanager_lazyloading: {
                    demand: demand
                },
                d: {
                  orderBy: {
                    sorting: 'product_'+demand.orderBy+','+demand.orderDirection
                  }
                }
            }) // Extbase doesn't understand json
        )
        var evt = new CustomEvent("ProductsLoaded", {detail: "Any Object Here"});
        window.dispatchEvent(evt);
        return result;
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
                },
              d: {
                orderBy: {
                  sorting: 'product_'+demand.orderBy+','+demand.orderDirection
                }
              }
            }) // Extbase doesn't understand json
        )
    }
}

export default LazyLoadingRequest;
