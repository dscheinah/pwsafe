import Component from '../lib/component.js';

/**
 * A component to handle re-login if a backend returns not authorized.
 * This component will be registered to all relevant scopes.
 */
class Reload extends Component {

    /**
     * Reloads the page if not authorized.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        if (data.error && data.code === 403) {
            window.location.reload();
        }
    }
}

export default Reload;