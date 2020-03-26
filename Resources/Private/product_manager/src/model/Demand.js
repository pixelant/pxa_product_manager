/**
 * Products lazy loading demand
 */
class Demand
{
    constructor(settings) {
        this.categories = settings.categories;
        this.storagePid = settings.storagePid;
        this.orderBy = settings.orderBy;
        this.orderDirection = settings.orderDirection;
        this.filterConjunction = settings.filterConjunction;
        this.limit = settings.limit;
        this.filters = settings.filters || {};
        this.offSet = 0;
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
     * Check if has filters
     *
     * @returns {boolean}
     */
    hasFilters() {
        return Object.keys(this.filters).length > 0;
    }

    /**
     * Convert demand to query string
     */
    asQueryParams() {
        let params = {};

        for (let property in this) {
            const value = this[property];
            params[property] = property === 'filters'
                ? JSON.stringify(value)
                : value;
        }

        return params;
    }
}

export default Demand;
