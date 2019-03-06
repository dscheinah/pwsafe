import Page from './page.js';

/**
 * Basic implementation to handle the browser navigation by the client.
 * Each opened page triggers a history change and vice versa.
 */
class Navigation {

    /**
     * Creates a new navigation instance. The history API is injectable to allow polyfills.
     *
     * @param {History} history
     */
    constructor(history) {
        if (!(history instanceof History)) {
            throw new TypeError('history must be instanceof History');
        }
        this.history = history;
        // Holds the data to map the popstate event state to pages. Uses the always incremented ID as the key.
        this.states = {};
        this.id = 0;
    }

    /**
     * Registers the popstate event listener. Each time the state changes the corresponding page will be opened.
     */
    start() {
        window.addEventListener('popstate', (e) => {
            let id = e.state, page = this.getPage(id);
            if (page) {
                page.show();
                // Reset the iterated ID to better match the browsers history index.
                this.id = id;
            }
        });
    }

    /**
     * Opens a page by adding it to the history and call it's show function.
     *
     * @param {Page} page
     */
    open(page) {
        if (!(page instanceof Page)) {
            throw new TypeError('page must be instanceof Page');
        }
        // Iterate the ID for the history state and save the page for the popstate event.
        this.id++;
        this.states[this.id] = page;
        if (Object.keys(this.states).length) {
            this.history.pushState(this.id, '', window.location.href);
        } else {
            // Replace the first state since the initial page view is not intended to show a page.
            // So the initial state is not relevant for the application.
            this.history.replaceState(this.id, '', window.location.href);
        }
        // Finally show the page to indicate success to the client.
        page.show();
    }

    /**
     * Returns the page saved for the given history state ID. If no page is registered null is returned.
     *
     * @param {int} id
     *
     * @returns {(Page|null)}
     */
    getPage(id) {
        return this.states[id] || null;
    }

    /**
     * Closes the currently open page by going back with the history.
     * Always close pages using this method to match the visible state with the histories state.
     */
    close() {
        this.history.go(-1);
    }
}

export default Navigation;
