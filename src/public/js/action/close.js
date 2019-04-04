import Action from '../lib/action.js';
import Navigation from '../lib/navigation.js';

/**
 * This action closes the current page by using the navigation.
 */
class Close extends Action {

    /**
     * Creates the action. The navigation will be used to close the active page.
     *
     * @param {Navigation} navigation
     */
    constructor(navigation) {
        if (!(navigation instanceof Navigation)) {
            throw new TypeError('navigation must instanceof Navigation');
        }
        super();
        this.navigation = navigation;
    }

    /**
     * Close the active page using the navigation after the state was updated.
     *
     * @param {Object} payload
     */
    run(payload) {
        if (!payload.error) {
            this.navigation.close();
        }
    }
}

export default Close;
