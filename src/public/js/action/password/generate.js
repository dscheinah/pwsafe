import Action from '../../lib/action.js';
import Backend from '../../lib/storages/backend.js';

/**
 * Action used to trigger password generation.
 */
class Generate extends Action {

    /**
     * Creates the action with the backend.
     *
     * @param backend
     */
    constructor(backend) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super();
        this.backend = backend;
    }

    /**
     * Trigger a save of the given buttons form on the backend. This will return a generated password. The action is
     * registered to a non submit button, since the submit will apply the generated password to the edit form.
     *
     * @param {HTMLButtonElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        return this.backend.run(this.backend.save, 'generate', trigger.form);
    }

    /**
     * Updates the generation form with the generated data.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return {
            generate: payload,
        };
    }
}

export default Generate;
