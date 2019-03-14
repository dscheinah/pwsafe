import Action from '../lib/action.js';
import Navigation from '../lib/navigation.js';

/**
 * Action used to copy data from the active to the target page using the submit of a form.
 * This will close the active page.
 */
class Apply extends Action {

    /**
     * Creates the action defining the target. The navigation will be used to close the active page.
     *
     * @param {string}     target
     * @param {Navigation} navigation
     */
    constructor(target, navigation) {
        if (!(navigation instanceof Navigation)) {
            throw new TypeError('navigation must instanceof Navigation');
        }
        super();
        this.target = target;
        this.navigation = navigation;
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

    /**
     * Close the active page using the navigation after the state was updated.
     *
     * @param {Object} payload
     */
    run(payload) {
        if (!payload.error) {
            this.navigation.close();
        }
    }

}

export default Apply;
