import Component from './component.js';

/**
 * This is the interface for all storage implementation. Use it to implement all kinds of storage engines.
 * It is implemented as a component to be able to store data from the application state.
 */
class Storages extends Component {

    /**
     * Creates a basic storage with data to set on component update.
     */
    constructor() {
        super();
        this.data = {};
    }

    /**
     * Sets the data to be used in implementing storages.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        this.data = data;
    }

    /**
     * Must be implemented to provide loading data for a given key with params.
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<{Object}>}
     */
    async load(key, params) {
        return {};
    }

    /**
     * Must be implemented to save data for a given key from the given form.
     *
     * @param {string}          key
     * @param {HTMLFormElement} form
     *
     * @returns {Promise<{Object}>}
     */
    async save(key, form) {
        return {};
    }

    /**
     * Must be implemented to remove data for a given key with params.
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<{Object}>}
     */
    async remove(key, params) {
        return {};
    }
}

export default Storages;
