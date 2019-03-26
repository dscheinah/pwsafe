import Action from './action.js';
import State from './state.js';

/**
 * Private helper to retrieve the real target with the action to dispatch from the event target.
 *
 * @param {HTMLElement=} target
 * @param {string}  [tag]
 *
 * @returns {(HTMLElement|null)}
 */
const actionFromTarget = function (target, tag) {
    if (!target) {
        return null;
    }
    do {
        // If the listener only requested a specific tag, the tagName must match.
        if (tag && target.tagName.toLowerCase() !== tag) {
            continue;
        }
        // The data-action attribute must be present for the element to be an usable target.
        if (target.dataset.action) {
            return target;
        }
        // If the parent is no HTMLElement, the end of the parent chain is reached and the search must be stopped.
    } while (target.parentNode instanceof HTMLElement && (target = target.parentNode));
    return null;
};

/**
 * Implements the dispatch handling of client and custom actions.
 * All event listeners should be registered with this class.
 */
class Actions {

    /**
     * Creates a new action handler with reference to the application state which should be changed on event dispatch.
     * The container will be used to register all event listeners. This is done to minimize the callbacks for events.
     *
     * @param {State} state
     * @param {(Element|Window)} container
     */
    constructor(state, container) {
        if (!(state instanceof State)) {
            throw new TypeError('state must be instanceof State');
        }
        this.state = state;
        this.container = container;
        // Collects all registered action instances for the action name.
        this.actions = {};
    }

    /**
     * Adds a new action instance to be executed if the given key is dispatched.
     * The keys must match the key from trigger. For events from listen this is the data-action attributes value of
     * the event target or its parents.
     *
     * @param {string} key
     * @param {Action} action
     */
    add(key, action) {
        if (!(action instanceof Action)) {
            throw new TypeError('action must be instanceof Action');
        }
        // Allow to collect multiple actions for the same key.
        if (!this.actions[key]) {
            this.actions[key] = [];
        }
        this.actions[key].push(action);
    }

    /**
     * Attaches a new event listener for the event to the container of this instance. If it is triggered the handler
     * will search for an event target or parent with a data-action attribute and dispatch the matching added actions.
     * If a tag name is given, only elements with the corresponding tag will be used to search the target or parent.
     *
     * @param {string}  event
     * @param {string=} tag
     */
    listen(event, tag) {
        if (tag) {
            // Check tag names insensitive.
            tag = tag.toLowerCase();
        }
        this.container.addEventListener(event, (e) => {
            // Search for the target or one of its parents with a data-action attribute.
            let target = actionFromTarget(e.target, tag);
            if (!target) {
                return;
            }
            // Check if there are action handlers added for the found action key.
            let action = target.dataset.action;
            if (!this.actions[action]) {
                return;
            }
            e.preventDefault();
            // Only have one client action at the same time running.
            if (this.isRunning) {
                return;
            }
            this.isRunning = true;
            // Dispatch the actions and prevent the event to toggle default behaviour, but only if actions are found.
            this.trigger(action, target).then(() => this.isRunning = false);
        });
    }

    /**
     * Used to trigger all actions added for the given key. This is used in the event listener and can be used
     * to dispatch actions from outside code.
     * This function needs to be async to handle all registered actions in sequential order with await.
     *
     * @param {string} key
     * @param {*=}     trigger
     */
    async trigger(key, trigger) {
        let actions = this.actions[key];
        if (!actions) {
            return;
        }
        let length = actions.length;
        for (let i = 0; i < length; i++) {
            let action = actions[i];
            // First create a payload from the given trigger. This returns a promise to resolve.
            let payload = await action.convert(trigger);
            // If resolved, use the action and payload to handle application state changes.
            this.state.dispatch(action, payload);
            // Finally allow the action to do changes independent from application state.
            action.run(payload);
        }
    }
}

export default Actions;
