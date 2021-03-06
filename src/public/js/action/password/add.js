import Action from '../../lib/action.js';
import Edit from './edit.js';

/**
 * Extends the edit action to fill the initial state with defaults to use the edit view template.
 * The template expects all values to be initialized.
 */
class Add extends Edit {

    /**
     * Initialize the edit action with fixed source and target.
     * The login scope contains the default user and email from the profile and is used as a source.
     *
     * @param {Page}    page
     * @param {Backend} backend
     */
    constructor(page, backend) {
        super(page, backend, 'defaults');
    }

    /**
     * Adds the name, url and notice defaults to the defaults added by the parent class.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        let defaults = {'id': '', 'name': '', 'url': '', 'notice': '', 'password': ''};
        data.password_edit = Action.combine('password_edit', defaults, data).password_edit;
        return data;
    }
}

export default Add;
