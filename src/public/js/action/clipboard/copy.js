import Action from '../../lib/action.js';
import Navigation from '../../lib/navigation.js';

/**
 * This action is used to trigger the clipboard component to copy a value to the clipboard.
 */
class Copy extends Action {

    /**
     * Creates the action storing the source scope to copy value from.
     * The navigation is needed to close the current page after copy from form submit.
     *
     * @param {string}      source
     * @param {Navigation=} navigation
     */
    constructor(source, navigation) {
        if (navigation && !(navigation instanceof Navigation)) {
            throw new TypeError('navigation must be instanceof Navigation');
        }
        super();
        this.source = source;
        this.navigation = navigation;
    }

    /**
     * Use the value of the pressed button to select the value from the source scope.
     * So the button value is given as the payloads key.
     *
     * @param {HTMLButtonElement|HTMLFormElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        if (trigger instanceof HTMLFormElement) {
            return {
                // Copy the first elements value and trigger a navigation close on run.
                // This is used for the password generation, opened from the profile page.
                key: trigger.querySelector('input').name,
                close: true,
            }
        }
        return {key: trigger.value};
    }

    /**
     * Fill the clipboard scope with the copied value from the source scope. If no value is found inside the source
     * an empty string will be copied.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return {
            clipboard: {
                value: Action.extract(state, this.source, payload.key) || '',
            },
        };
    }

    /**
     * Closes the current page if run from a form submit.
     *
     * @param {Object} payload
     */
    run(payload) {
        if (this.navigation && payload.close) {
            this.navigation.close();
        }
    }
}

export default Copy;
