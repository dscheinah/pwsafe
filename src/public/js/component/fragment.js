import Actions from '../lib/actions.js';
import Component from '../lib/component.js';

/**
 * Creates the FormData object with the given term. The leading # is removed.
 *
 * @param {string} term
 *
 * @return {FormData}
 */
const createFormData = function(term) {
    let form = new FormData();
    form.append('term', term.replace(/^#/, ''));
    return form;
};

/**
 * This component is used to trigger the automatic search if the page is opened with a fragment.
 * The fragment needs to be removed by some action to not trigger the search each time the passwords are shown.
 */
class Fragment extends Component {

    /**
     * Create the component with a reference to the actions.
     * On update of categories and passwords scope the actions will be used to trigger search and password select.
     *
     * @param {Actions} actions
     */
    constructor(actions) {
        if (!(actions instanceof Actions)) {
            throw new TypeError('actions must be instanceof Actions');
        }
        super();
        this.actions = actions;
    }

    /**
     * If a fragment is given this component triggers the search and select password chain.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        super.update(data, scope);
        switch (scope) {
            // The categories scope is updated once the password page launches.
            case 'categories':
                // If no fragment is given, the search will not trigger.
                if (!window.location.hash) {
                    return;
                }
                // Trigger the search with a simulated form containing the pre selected search term.
                let form = createFormData(window.location.hash);
                // Remove the hash to not trigger the automatic search twice.
                window.location.hash = '';
                // Set the trigger to enable automatic password selection if only one result is found.
                this.actions.trigger('password_search', form).then(() => {
                    // This was set by the call to this.update with the passwords scope from within the search action.
                    if (!this.id) {
                        return Promise.resolve();
                    }
                    // Simulate a click on the details button.
                    return this.actions.trigger('password', {value: this.id});
                }).then(() => delete this.id);
                break;
            case 'passwords':
                // If there is exactly one password result, remember it's ID to show it after the search is done.
                // The update for the passwords is run before the search trigger is fulfilled.
                if (data.list && data.list.length === 1) {
                    this.id = data.list[0].id;
                }
                break;
        }
    }
}

export default Fragment;
