import Storages from '../storages.js';

/**
 * Implements the localStorage API as a Storages.
 */
class Local extends Storages {

    /**
     * Creates the instance with a given prefix and a version to separate different scopes.
     * The localStorage instance is injected to allow the use of polyfills.
     *
     * @param {Storage}         storage
     * @param {string}          prefix
     * @param {(string|number)} version
     */
    constructor(storage, prefix, version) {
        super();
        this.storage = storage;
        this.prefix = prefix;
        this.version = version;
    }

    /**
     * The localStorage does not implement an explicite save but just updates as a component from the application state.
     * This is done since the localStorage is seen as a persistent extension to the application state.
     * There must be an init action to load the data into the initial application state.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        // Use the prefix and version to create a unique key in the key value storage.
        // Since the localStorage only accepts strings the data must be JSON encoded.
        this.storage.setItem([this.prefix, scope, this.version].join('-'), JSON.stringify(data));
    }

    /**
     * Retrieves data from the localStorage as it was saved in update. The params are ignored.
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<Object>}
     */
    async load(key, params) {
        return JSON.parse(this.storage.getItem([this.prefix, key, this.version].join('-')));
    }
}

export default Local;
