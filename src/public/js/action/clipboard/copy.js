import Action from '../../lib/action.js';

/**
 * This action is used to trigger the clipboard component to copy a value to the clipboard.
 */
class Copy extends Action {

    /**
     * Creates the action storing the source scope to copy value from.
     *
     * @param {string} source
     */
    constructor(source) {
        super();
        this.source = source;
    }

    /**
     * Use the value of the pressed button to select the value from the source scope.
     * So the button value is given as the payloads key.
     *
     * @param {HTMLButtonElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
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
}

export default Copy;
