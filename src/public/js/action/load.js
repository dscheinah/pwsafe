import Action from '../lib/action.js';
import Backend from '../lib/storages/backend.js';
import Open from './open.js';

/**
 * This actions loads data from the PHP backend to the application state and opens the corresponding page.
 */
class Load extends Open {

    /**
     * Create the action with a reference to the backend. The target is used as scope and path for the PHP call.
     *
     * @param {Page}    page
     * @param {string}  target
     * @param {Backend} backend
     */
    constructor(page, target, backend) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super(page);
        this.target = target;
        this.backend = backend;
    }

    /**
     * Loads the data to be used for loading lists or entries.
     *
     * @param {(HTMLButtonElement|*)=} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        let data = {};
        // If given a button this action will load exactly one entry with the ID from the buttons value attribute.
        if (trigger && trigger.value) {
            data.id = trigger.value;
        }
        return this.backend.run(this.backend.load, this.target, data);
    }

    /**
     * Apply the loaded data to the application state.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return Action.combine(this.target, payload, state);
    }
}

export default Load;
