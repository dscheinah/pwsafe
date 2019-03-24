import Parent from '../edit.js';

/**
 * Extends the default edit action to allow rendering the category select.
 */
class Edit extends Parent {

    /**
     * Use fixed scopes for edit. The source is given if the class is used for the add action.
     *
     * @param {Page}    page
     * @param {string=} source
     */
    constructor(page, source) {
        super(page, source || 'password', 'password_edit');
    }

    /**
     * Always append an empty category object to the scope. This is needed to trigger the update of the select part.
     * The part will take the category_id from the parent scope and the categories list from the main state.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @return {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        data.password_edit.categories = {};
        return data;
    }
}

export default Edit;