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

    /**
     * Checks if the rendered password overflows the available content to remove the inline width by CSS.
     *
     * @param {Object} payload
     */
    run(payload) {
        super.run(payload);
        let table = this.page.template.container.querySelector('table');
        if (table && table.getBoundingClientRect().right > window.innerWidth) {
            table.classList.add('overflow');
        }
    }
}

export default Detail;
