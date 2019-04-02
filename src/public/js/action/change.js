import Action from '../lib/action.js';

/**
 * An action to apply changes of an form field to a scope.
 */
class Change extends Action {

    /**
     * Creates the action with the target scope.
     *
     * @param {string} scope
     */
    constructor(scope) {
        super();
        this.scope = scope;
    }

    /**
     * Extracts the input data from the form field.
     *
     * @param {HTMLElement} trigger
     *
     * @return {Promise<{Object}>}
     */
    async convert(trigger) {
        let data = {};
        data[trigger.name] = trigger.value;
        return data;
    }

    /**
     * Applies the value to the scope.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return Action.combine(this.scope, payload, state);
    }
}

export default Change;
