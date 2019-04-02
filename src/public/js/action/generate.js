import Action from '../lib/action.js';
import Open from './open.js';

/**
 * An action to open the generate page with defaults.
 * This is used to render different buttons for password and profile scope.
 */
class Generate extends Open {

    /**
     * Creates the actions with the defaults for the generate scope.
     *
     * @param {Page}    page
     * @param {boolean} apply
     */
    constructor(page, apply) {
        super(page);
        this.apply = apply;
    }

    /**
     * Applies the defaults from the constructor.
     *
     * @param state
     * @param payload
     */
    reduce(state, payload) {
        let data = {
            apply: this.apply,
            // Always empty the previous generated password on open to not reuse a password.
            password: '',
        };
        let combined = Action.combine('generate', data, state);
        if (!combined.generate.type) {
            // Initialize the pre-selected value for the first page view.
            combined.generate.type = 'random';
        }
        return combined;
    }
}

export default Generate;
