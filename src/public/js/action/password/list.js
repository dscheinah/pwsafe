import Action from '../../lib/action.js';
import Backend from '../../lib/storages/backend.js';
import Open from '../open.js';

/**
 * Handler to initialize the password list.
 */
class List extends Open {

    /**
     * Creates the handler with a reference to the page to open and the backend to load the data from.
     *
     * @param {Page}    page
     * @param {Backend} backend
     */
    constructor(page, backend) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super(page);
        this.backend = backend;
    }

    /**
     * Loads the categories from the backend. This will render the filters but no passwords.
     * The user needs to select a category or use the search to load passwords from the backend.
     *
     * @param {*} trigger
     *
     * @return {Promise<{Object}>}
     */
    async convert(trigger) {
        return this.backend.run(this.backend.load, 'categories');
    }

    /**
     * Simply put the loaded categories inside the state.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @return {Object}
     */
    reduce(state, payload) {
        if (!state.categories || !state.categories.category_id) {
            payload.category_id = '';
        }
        return {
            categories: Action.combine('categories', payload, state).categories,
            // Still create the scope to have the search rendered.
            passwords: {},
        };
    }
}

export default List;