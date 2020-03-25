/**
 * Save original categories, so we can set it if categories filter was reset
 */
let _originalCategories = null;

/**
 * Products lazy loading demand
 */
class Demand
{
    constructor(settings) {
        this.categories = _originalCategories = settings.categories;
        this.storagePid = settings.storagePid;
        this.orderBy = settings.orderBy;
        this.orderDirection = settings.orderDirection;
        this.filterConjunction = settings.filterConjunction;
        this.limit = settings.limit;
        this.attributes = {};
        this.offSet = 0;
    }

    /**
     * Update attributes. Method called after filter update event
     *
     * @param filter
     * @param selectedOptions
     */
    updateFilter(filter, selectedOptions) {
        // If filter is categories just update categories
        if (filter.type === 1) {
            this.updateCategories(selectedOptions);
            return;
        }

        // If attributes filter
        let id = filter.attributeUid;

        this.attributes[id] = {
            conjunction: filter.conjunction,
            value: [],
        };

        for (let option in selectedOptions) {
            let value = selectedOptions[option].value;

            this.attributes[id].value.push(value);
        }
    }

    /**
     * Update categories filter
     *
     * @param selectedOptions
     */
    updateCategories(selectedOptions) {
        this.categories = [];

        for (let option in selectedOptions) {
            let value = selectedOptions[option].value;

            this.categories.push(value);
        }

        if (this.categories.length === 0) {
            this.categories = _originalCategories;
        }
    }
}

export default Demand;
