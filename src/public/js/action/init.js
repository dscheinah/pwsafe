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
        return trigger.load('login');
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
            // The generation scope must be initialized for the generation form to be rendered. Without any data
            // the templates will not render since the pages update method is never called.
            generate: {},
        };
    }
}

export default Init;
