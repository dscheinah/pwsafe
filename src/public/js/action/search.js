import Action from '../lib/action.js';
import Backend from '../lib/storages/backend.js';

/**
 * This class represents the action to update data on a visible page by requesting from a form submit.
 */
class Search extends Action {

    /**
     * Create the search with a target scope and the backend to request.
     *
     * @param {string}  target
     * @param {Backend} backend
     */
    constructor(target, backend) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instance of Backend');
        }
        super();
        this.target = target;
        this.backend = backend;
    }

    /**
     * Sends the search form to the backend to request the new data.
     *
     * @param {HTMLFormElement|FormData} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        // Do a save since this simply represents a POST.
        return this.backend.run(this.backend.save, this.target, trigger);
    }

    /**
     * Update the list.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = Action.combine(this.target, payload, state);
        // Also set the loaded category ID into the state to mark the correct filter button.
        data.categories = Action.combine('categories', {category_id: payload.category_id}, state).categories;
        return data;
    }
}

export default Search;
