import Action from '../lib/action.js';

class Loading extends Action {

    /**
     * Creates the loading action with the target state.
     * Use true for the load start action and false for end.
     *
     * @param {boolean} state
     */
    constructor(state) {
        super();
        this.state = state;
    }

    /**
     * If the action is called write the state to the loading scope.
     * The menu component will be triggered and render the correct state.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @return {Object}
     */
    reduce(state, payload) {
        // Trigger no rendering if the state is already correctly set.
        if (state.loading && state.loading.state === this.state) {
            return {};
        }
        return {loading: {state: this.state}};
    }
}

export default Loading;