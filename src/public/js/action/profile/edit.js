import Parent from '../edit.js';

/**
 * Extends the default edit action for use with the profile menu entry.
 */
class Edit extends Parent {

    /**
     * Creates the edit action with fixed defaults.
     *
     * @param {Page} page
     */
    constructor(page) {
        super(page, 'login', 'profile');
    }

    /**
     * Always empties the password to never print it in templates.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        // Always set an empty password for editing since empty values will not be saved.
        // This way the clients password will not be visible to anyone using the application with his login.
        data.profile.password = '';
        return data;
    }
}

export default Edit;
