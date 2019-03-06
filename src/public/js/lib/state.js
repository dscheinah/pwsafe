import Action from './action.js';
import Component from './component.js';

/**
 * A simple helper to clone objects. This is needed to never let the application state be changed outside of the State.
 *
 * @param {Object} data
 *
 * @returns {Object}
 */
const clone = function (data) {
    return JSON.parse(JSON.stringify(data));
};

/**
 * Represents the global application state. It can only be changed by dispatching an action. On change it handles
 * updates of registered components.
 */
class State {

    /**
     * Create the state allowing to store the state and components.
     */
    constructor() {
        this.state = {};
        this.components = {};
    }

    /**
     * Adds a new component to be updated on state changes for the given scope.
     * It also forwards the current application state to the component if available for the scope.
     *
     * @param {string}    scope
     * @param {Component} component
     */
    register(scope, component) {
        if (!(component instanceof Component)) {
            throw new TypeError('component must be instanceof Component');
        }
        // Allow to always register multiple components for the same scope.
        if (!this.components[scope]) {
            this.components[scope] = [];
        }
        this.components[scope].push(component);
        let state = this.state[scope];
        if (state) {
            // Trigger update if the state contains a value for the scope.
            // Only give a copy to not allow components to mess with the application state.
            component.update(clone(state), scope);
        }
    }

    /**
     * Dispatches an action with the given payload. This is the only way to trigger changes on the application state.
     * If the action returns new data from its reduce function, all components registered for the scopes of the new
     * data are updated.
     *
     * @param {Action} action
     * @param {Object} payload
     */
    dispatch(action, payload) {
        if (!(action instanceof Action)) {
            throw new TypeError('action must be instanceof Action');
        }
        // Only give a clone of the state to only allow changing it by returning a new state.
        const state = action.reduce(clone(this.state), payload);
        // Only scopes present in the new state are updated.
        for (let scope in state) {
            let data = state[scope], components = this.components[scope];
            // Update the state for the scope and update all components registered for it.
            this.state[scope] = data;
            if (components) {
                // Only give a copy to not allow components to mess with the application state.
                components.forEach((component) => component.update(clone(data), scope));
            }
        }
    }
}

export default State;
