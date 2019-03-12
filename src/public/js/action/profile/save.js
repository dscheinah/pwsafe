import Action from '../../lib/action.js';
import Parent from '../save.js';

/**
 * Action to save the changed profile data.
 */
class Save extends Parent {

    /**
     * Creates the action with a fixed path for PHP backend calls.
     *
     * @param {Navigation} navigation
     * @param {Backend}    backend
     */
    constructor(navigation, backend) {
        super('profile', navigation, backend);
    }

    /**
     * Updates the saved data to the profile scope and login defaults (used for localStorage and new passwords).
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        // Update the profile edit page if the client uses navigation forward.
        let data = Action.combine('profile', payload, state);
        if (!payload.error) {
            // The defaults scope is used as a source for new password defaults.
            data.defaults = data.profile;
            // And the login scope for the stored user name on login.
            data.login = Action.combine('login', {user: payload.user}, state).login;
            console.log(data);
        }
        return data;
    }
}

export default Save;
