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
        this.status = 0;
        this.message = '';
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
     * Set the error property to not zero and the message if available to indicate an error status.
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
     * Set the status property to not zero and the message if available to indicate an error status.
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
     * Set the status property to not zero and the message if available to indicate an error status.
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<{Object}>}
     */
    async remove(key, params) {
        return {};
    }

    /**
     * If the implementing storage got an error in one of the async functions, it returns the set error code and the
     * message as an object. The implementation must set the status and message properties.
     * The return value contains three keys: error which is true, code and message.
     *
     * @returns {Object|null}
     */
    error() {
        if (!this.status) {
            return null;
        }
        return {
            error: true,
            code: this.status,
            message: this.message || '',
        };
    }

    /**
     * A small helper to wrap the storage calls and error checks.
     * It returns the error if existent and the data from the callback if no error.
     *
     * @param {Function} callback
     * @param {*}        params
     *
     * @returns {Promise<{Object}>}
     */
    async run(callback, ...params) {
        let data = await callback.apply(this, params);
        return this.error() || data;
    }
}

export default Storages;
