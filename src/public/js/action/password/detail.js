import Load from '../load.js';

/**
 * This actions loads password details from the PHP backend.
 */
class Detail extends Load {

    /**
     * Creates the action using a fixed scope.
     *
     * @param {Page}    page
     * @param {Backend} backend
     */
    constructor(page, backend) {
        super(page, 'password', backend);
    }

    /**
     * Loads the data using the value of the trigger button as ID.
     *
     * @param {HTMLButtonElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        let data = await this.backend.load('password', {id: trigger.value}), error = this.backend.error();
        return error || data;
    }

    /**
     * The passwords will not be visible by default if not clicking on the toggle button.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        let data = super.reduce(state, payload);
        data.password.visible = false;
        return data;
    }
}

export default Detail;