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
     * @param url
     */
    constructor(url) {
        this.url = url;
    }

    /**
     * Load products with demand
     *
     * @param demand
     * @returns {*}
     */
    load(demand) {
        return axios.post(
            this.url,
            objectToFormData({
                tx_pxaproductmanager_pi1: {
                    demand: demand
                }
            }) // Extbase doesn't understand json
        )
    }
}

export default LazyLoadingRequest;
