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
     * @param {Navigation} navigation
     * @param {string}     target
     * @param {Backend}    backend
     * @param {string=}    list
     */
    constructor(navigation, target, backend, list) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super(navigation, target);
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
        // Update the DOM to match the form elements internal values.
        // This is needed to fix change detection in template rendering.
        for (let i = trigger.elements.length; i--;) {
            let element = trigger.elements[i];
            // This is not correct for most form element types. But it is sufficient for the use case.
            element.setAttribute('value', element.value);
        }
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
        if (!this.list || payload.error) {
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
}

export default Save;
