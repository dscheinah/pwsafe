import Component from '../lib/component.js';

/**
 * A component to handle re-login if a backend returns not authorized.
 * This component will be registered to all relevant scopes.
 */
class Reload extends Component {

    /**
     * Reloads the page if not authorized or timed out.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        if (data.error && data.code === 403) {
            window.location.reload();
        }
        // Reset the timeout on any action.
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
        // Reload after 10 minutes of inactivity to lock the app.
        this.timeout = setTimeout(() => {
            window.location.reload();
        }, 600000);
    }
}

export default Reload;