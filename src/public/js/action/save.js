import Action from '../lib/action.js';
import Apply from './apply.js';
import Backend from '../lib/storages/backend.js';

/**
 * An action to save data to the PHP backend. Also all changes are applied to the detail and list page scopes.
 */
class Save extends Apply {

    /**
     * Creates the action with all needed names and references.
     * The list is optional as not all forms have a corresponding list.
     *
     * @param {string}     target
     * @param {Navigation} navigation
     * @param {Backend}    backend
     * @param {string=}    list
     */
    constructor(target, navigation, backend, list) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super(target, navigation);
        this.backend = backend;
        this.list = list;
    }

    /**
     * Saves the data from the form using the provided PHP backend.
     *
     * @param {HTMLFormElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        return this.backend.run(this.backend.save, this.target, trigger);
    }

    /**
     * Updates the detail page using the apply action (super) and also update the corresponding entry in the list scope.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        if (!this.list) {
            return data;
        }
        // Always assume list inside the list. This is reasonable as lists are always implemented with page parts.
        let updated = false, list = Action.extract(state, this.list, 'list') || [];
        list.forEach((entry, key) => {
            if (entry.id === payload.id) {
                list[key] = payload;
                updated = true;
            }
        });
        // If an entry was added it will be appended last. It will not be inserted in order.
        if (!updated) {
            list.push(payload);
        }
        // Merge the details with the list changes.
        data[this.list] = state[this.list] || {};
        data[this.list].list = list;
        return data;
    }

    /**
     * Focus the first empty input after error.
     *
     * @param {Object} payload
     */
    run(payload) {
        super.run(payload);
        if (payload.error) {
            let input = this.page.template.container.querySelector('input[value=""], input:not([value])');
            if (input) {
                input.focus();
            }
        }
    }
}

export default Save;
