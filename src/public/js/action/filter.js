import Action from '../lib/action.js';
import Actions from '../lib/actions.js';

/**
 * An action to trigger automatic submit on changes.
 */
class Filter extends Action {

    /**
     * Creates the filter action with a reference to the actions handler to trigger submit.
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
     * Run the submit on the parent form of the changed input element.
     *
     * @param {HTMLInputElement} trigger
     *
     * @return {Promise<{Object}>}
     */
    async convert(trigger) {
        let form = trigger.form;
        await this.actions.trigger(form.dataset.action, form);
        return {};
    }
}

export default Filter;
