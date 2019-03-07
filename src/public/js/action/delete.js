import Action from '../lib/action.js';
import Backend from '../lib/storages/backend.js';

/**
 * Delete an entry from the database (PHP backend) and list. This action will ask for a confirmation.
 */
class Delete extends Action {

    /**
     * Creates the action. It will be removed from the given list scopes list property.
     * The key will be used with the given backend.
     *
     * @param {string}  list
     * @param {Backend} backend
     * @param {string}  key
     * @param {string}  message
     */
    constructor(list, backend, key, message) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super();
        this.list = list;
        this.backend = backend;
        this.key = key;
        this.message = message;
    }

    /**
     * If the client confirms the given confirmation box with the message from the constructor, the buttons value
     * will be used to delete an entry with the given ID from the backend.
     * It expects the backend to return an object with the deleted ID to be used as the payload.
     *
     * @param {HTMLButtonElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        if (confirm(this.message)) {
            let data = await this.backend.remove(this.key, {id: trigger.value}), error = this.backend.error();
            return error || data;
        }
        return {};
    }

    /**
     * Remove the entry with the in the payload given ID from the list property of the list scope.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        // If not confirmed or no success in backend, no ID is given. So do not change the state.
        if (!payload.id && !payload.error) {
            return {};
        }
        if (payload.error) {
            return Action.combine(this.list, payload, state);
        }
        // It is save to assume a list property for all lists since lists are always rendered by page parts.
        let list = Action.extract(state, this.list, 'list') || [];
        // Filter out the deleted entry and replace the list inside the original scope.
        list = list.filter(entry => entry.id !== payload.id);
        // Reset error state if no error.
        return Action.combine(this.list, {list: list, error: false}, state);
    }
}

export default Delete;
