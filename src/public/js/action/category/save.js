import Action from '../../lib/action.js';
import Parent from '../save.js';

/**
 * Extended save action for categories.
 */
class Save extends Parent {

    /**
     * Use fixed scopes for the parent save action.
     *
     * @param {Navigation} navigation
     * @param {Backend}    backend
     */
    constructor(navigation, backend) {
        super('category', navigation, backend, 'categories');
    }

    /**
     * Also write the newly created category id to the password edit scope, as the add category action is also available
     * from the password edit page. If a category is added it is assumed to be used by the client right afterwards.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        data.password_edit = Action.combine(
            'password_edit', {category_id: data.category.id}, state
        ).password_edit;
        return data;
    }
}

export default Save;