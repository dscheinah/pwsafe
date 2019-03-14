import Action from '../../lib/action.js';
import Open from '../open.js';

class Add extends Open {

    /**
     * Adds the name as an empty default to be able to render the template.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        // Also set the ID empty to not replace previously created categories.
        return Action.combine('category', {'id': '', 'name': ''}, state);
    }
}

export default Add;