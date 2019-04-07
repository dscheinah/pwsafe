import Action from '../lib/action.js';
import Open from './open.js';

/**
 * The action to open a page with default values. This is used to create formulars for adding entries.
 */
class Add extends Open {

    /**
     * Creates the action with the target scope and its defaults.
     *
     * @param {Page}   page
     * @param {string} scope
     * @param {Object} defaults
     */
    constructor(page, scope, defaults) {
        super(page);
        this.scope = scope;
        this.defaults = defaults;
    }

    /**
     * Adds the defaults to be able to render the template and reset previous data.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return Action.combine(this.scope, this.defaults, state);
    }
}

export default Add;
