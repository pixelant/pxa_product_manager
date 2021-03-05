/**
 * Products lazy loading demand
 */
class Demand
{
    constructor(settings) {
        this.storagePid = settings.storagePid || {};
        this.pageTreeStartingPoint = settings.pageTreeStartingPoint;
        this.orderBy = settings.orderBy;
        this.orderDirection = settings.orderDirection;
        this.filterConjunction = settings.filterConjunction;
        this.hideFilterOptionsNoResult = parseInt(settings.hideFilterOptionsNoResult);
        this.limit = parseInt(settings.limit);
        this.filters = settings.filters || {};
        this.offSet = parseInt(settings.offSet || 0);
    }

    /**
     * Update filters. Method called after filter update event
     *
     * @param filter
     * @param selectedOptions
     */
    updateFilter(filter, selectedOptions) {
        // If filters filter
        let id = filter.uid;

        // If not options, just unset filter attribute
        if (Object.keys(selectedOptions).length === 0) {
            delete this.filters[id];
            return;
        }

        this.filters[id] = {
            conjunction: filter.conjunction,
            attribute: filter.attributeUid,
            type: filter.type,
            value: [],
        };

        for (let option in selectedOptions) {
            let value = selectedOptions[option].value;

            this.filters[id].value.push(value);
        }
    }

    /**
     * Check if has value for query string
     *
     * @returns {boolean}
     */
    hasQueryStringChanges() {
        return Object.keys(this.filters).length > 0 || this.offSet > 0;
    }

    /**
     * Convert demand to query string
     */
    asQueryParams() {
        let params = {};
        let arrayProperties = ['filters', 'storagePid'];

        for (let property in this) {
            const value = this[property];

            params[property] = arrayProperties.includes(property)
                ? JSON.stringify(value)
                : value;
        }

        return params;
    }

    updateOrderby(orderBy, orderDirection) {
        this.orderBy = orderBy;
        this.orderDirection = orderDirection;
    }
}

export default Demand;
