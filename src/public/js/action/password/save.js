import Action from '../../lib/action.js';
import Parent from '../save.js';

class Save extends Parent {

    /**
     * Use fixed scopes for parent class.
     *
     * @param {Backend}    backend
     * @param {Navigation} navigation
     */
    constructor(navigation, backend) {
        super(navigation, 'password', backend, 'passwords');
    }

    /**
     * The error needs to be rendered in the form.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        if (payload.error) {
            return Action.combine('password_edit', payload, state);
        }
        return super.reduce(state, payload);
    }
}

export default Save;
