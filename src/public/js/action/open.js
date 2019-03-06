import Action from '../lib/action.js';
import Page from '../lib/page.js';

/**
 * An action to simply enable a page. This is used as a base action for other actions.
 */
class Open extends Action {

    /**
     * Create the action with the page to open on run.
     *
     * @param {Page} page
     */
    constructor(page) {
        if (!(page instanceof Page)) {
            throw new TypeError('page must be instanceof Page');
        }
        super();
        this.page = page;
    }

    /**
     * Opens the page by enabling it.
     *
     * @param {Object} payload
     */
    run(payload) {
        this.page.enable();
    }
}

export default Open;
