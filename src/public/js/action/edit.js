import Action from '../lib/action.js';
import Open from './open.js';

/**
 * Action to trigger opening the edit page. It copies all values from the detail scope to the edit scope.
 */
class Edit extends Open {

    /**
     * Create the action with the source and target scope key.
     *
     * @param {Page}   page
     * @param {string} source
     * @param {string} target
     */
    constructor(page, source, target) {
        super(page);
        this.source = source;
        this.target = target;
    }

    /**
     * Copies the values from the source to the target.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return Action.combine(this.target, state[this.source] || {});
    }
}

export default Edit;
