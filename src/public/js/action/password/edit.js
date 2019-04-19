import Backend from '../../lib/storages/backend.js';
import Parent from '../edit.js';

/**
 * Extends the default edit action to allow rendering the category select.
 */
class Edit extends Parent {

    /**
     * Use fixed scopes for edit. The source is given if the class is used for the add action.
     *
     * @param {Page}    page
     * @param {Backend} backend
     * @param {string=} source
     */
    constructor(page, backend, source) {
        if (!(backend instanceof Backend)) {
            throw new TypeError('backend must be instanceof Backend');
        }
        super(page, source || 'password', 'password_edit');
        this.backend = backend;
    }

    /**
     * Loads the base data for the share part.
     *
     * @param {*} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        return this.backend.run(this.backend.load, 'share');
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
        // Apply the groups and users available to share passwords to.
        data.share = payload;
        // To have access to the data inside the part.
        data.share.share_groups = data.password_edit.share_groups || [];
        data.share.share_users = data.password_edit.share_users || [];
        return data;
    }
}

export default Edit;
