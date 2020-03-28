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
    loadProducts(demand) {
        return this._submitDemand(this.url, demand);
    }

    /**
     * Request to count all products and load available options
     *
     * @param demand
     */
    loadAvailableOptions(demand) {
        return this._submitDemand(this._getActionUrl('list', 'LazyAvailableFilters'), demand);
    }

    /**
     * Submit demand
     *
     * @param url
     * @param demand
     * @private
     */
    _submitDemand(url, demand) {
        return axios.post(
            url,
            objectToFormData({
                tx_pxaproductmanager_pi1: {
                    demand: demand
                }
            }) // Extbase doesn't understand json
        )
    }

    /**
     * Api URL + action
     *
     * @param action
     * @param controller
     * @returns string
     * @private
     */
    _getActionUrl(action, controller = null) {
        let actionQuery = '&tx_pxaproductmanager_pi1[action]=' + action;
        if (controller) {
            actionQuery += '&tx_pxaproductmanager_pi1[controller]=Api\\' + controller;
        }

        const url = this.url;

        return url.includes('?') ? (url + actionQuery) : (url + actionQuery.substring(1));
    }
}

export default LazyLoadingRequest;
