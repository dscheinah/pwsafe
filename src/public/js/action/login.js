import Action from '../lib/action.js';
import Actions from '../lib/actions.js';
import Backend from '../lib/storages/backend.js';

/**
 * Action to handle login. If the login succeeds the passwords list will be loaded.
 */
class Login extends Action {

    /**
     * Creates the action with a reference to the PHP backend for login validation and the actions to trigger loading
     * the password list.
     *
     * @param {Backend} backend
     * @param {Actions} actions
     */
    constructor(backend, actions) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        if (!(actions instanceof Actions)) {
            throw new TypeError('actions must be instanceof Actions');
        }
        super();
        this.backend = backend;
        this.actions = actions;
    }

    /**
     * Handles password validation. The PHP backend returns the profile data.
     * Also the entered password from the input field is removed to not keep it visible to people using the app with
     * the clients login.
     *
     * @param {HTMLFormElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        let data = await this.backend.save('login', trigger), error = this.backend.error();
        if (error) {
            return error;
        }
        // Use a delay to not make the validation visible while the page is still active.
        setTimeout(() => trigger.elements['password'].value = '', 500);
        return data;
    }

    /**
     * Updates the application state with the user data. The entered user name will be saved into the localStorage
     * for the next login. Also the key is given to the backend registered to the application state.
     * A valid session (acquired by the convert method) and the key are required to work with the PHP backend.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        if (payload.error) {
            return Action.combine('login', payload, state);
        }
        let user = state.user || {};
        user.key = payload.key;
        // The key must not be saved to the localStorage.
        delete payload.key;
        return {
            defaults: payload,
            login: {
                user: payload.user,
            },
            user: user,
        };
    }

    /**
     * If the application state is prepared for further interaction with the PHP backend, load the passwords.
     * This will trigger the passwords page to be opened next.
     *
     * @param {Object} payload
     */
    run(payload) {
        if (!payload.error) {
            this.actions.trigger('passwords').then();
        }
    }
}

export default Login;
