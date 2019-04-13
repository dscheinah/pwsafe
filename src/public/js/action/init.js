import Open from './open.js';
import Storages from '../lib/storages.js';

/**
 * This is the main action triggered by the app.js to fill the initial state and open the login page.
 */
class Init extends Open {

    /**
     * The trigger is given by the calling JavaScript. It is the localStorage containing the saved user name.
     *
     * @param {Storages} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        if (!(trigger instanceof Storages)) {
            throw new TypeError('trigger must be instanceof Storages');
        }
        let login = await trigger.load('login');
        return (login instanceof Object) ? login : {};
    }

    /**
     * Creates the initial application state.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        // The login form expects the user to be set.
        if (!payload.user) {
            payload.user = '';
        }
        // Do not restore the error state from previous failed attempts.
        payload.error = false;
        return {
            login: payload,
            current: {},
        };
    }

    /**
     * Focus the empty input field.
     *
     * @param {Object} payload
     */
    run(payload) {
        super.run(payload);
        // Focus is only possible if the element is in DOM, so run super first.
        this.page.template.container.querySelector(`[name=${payload.user ? 'password' : 'user'}]`).focus();
    }
}

export default Init;
