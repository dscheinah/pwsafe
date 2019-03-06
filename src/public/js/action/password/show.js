import Action from '../../lib/action.js';

/**
 * An action to toggle visibility of passwords on the detail page.
 */
class Show extends Action {

    /**
     * Toggle the visible flag inside the application state. The template will then not use the mask helper.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = state.password || {};
        data.visible = !data.visible;
        return {
            password: data,
        };
    }
}

export default Show;
