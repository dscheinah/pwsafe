import Action from '../lib/action.js';
import Close from './close.js';
import Navigation from '../lib/navigation.js';

/**
 * Action used to copy data from the active to the target page using the submit of a form.
 * This will close the active page.
 */
class Apply extends Close {

    /**
     * Creates the action defining the target. The navigation will be used to close the active page.
     *
     * @param {Navigation} navigation
     * @param {string}     target
     */
    constructor(navigation, target) {
        super(navigation);
        this.target = target;
    }

    /**
     * Extracts all values of the submitted form into an object.
     *
     * @param {HTMLFormElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        let data = {};
        Array.from(trigger.elements).forEach((element) => {
            // Ignore elements without name. This is usually the submit button.
            if (element.name) {
                data[element.name] = element.value;
            }
        });
        return data;

    }

    /**
     * Copy the extracted from data to the target scope.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        // Updates all data in the scope of state but keep existing values.
        return Action.combine(this.target, payload, state);
    }
}

export default Apply;
