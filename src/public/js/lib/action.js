/**
 * Defines all methods an action can provide. The methods will be called when by triggering the Actions instance.
 * An action does not need to implement all since reasonable defaults are provided.
 */
class Action {

    /**
     * Is called before applying any changes to the application state. The given trigger depends on the action.
     * For client event based actions it will be events target.
     * Once the returned promise resolves the payload of the promise will be used to change the application state.
     * For this the reduce function of this action is called.
     *
     * @param {*=} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        return {};
    }

    /**
     * Is called by the application state after the events trigger is converted into a payload by the convert method.
     * The object returned will cause the application state to update all its components.
     * Each key present in main level of the returned object will be completely updated. It is advised to only return
     * updated keys for performance reasons.
     *
     * @param {Object} state
     * @param {Object} payload
     *
     * @returns {Object}
     */
    reduce(state, payload) {
        return {};
    }

    /**
     * After the application state and its components has been updated by reduce this method is called.
     * It can be used to trigger changes outside of the state like opening or closing pages.
     *
     * @param {Object} payload
     */
    run(payload) {
    }

    /**
     * Can be used by extending classes to extract data from an object at the given key path. If any part of the path
     * is not defined, null will be returned.
     * This is used to reduce boilerplate checks.
     *
     * @param {Object} data
     * @param {string} keys
     *
     * @returns {*|null}
     */
    static extract(data, ...keys) {
        let length = keys.length;
        for (let i = 0; i < length; i++) {
            let key = keys[i];
            if (!data[key]) {
                return null;
            }
            data = data[key];
        }
        return data;
    }

    /**
     * Creates a new object or updates an existing if the state is given. The function returns an object only
     * containing the scope key to be used as the return value of reduce.
     * This can be used in extending classes to avoid boilerplate object creation.
     *
     * @param {string}  scope
     * @param {Object}  data
     * @param {Object=} state
     *
     * @returns {Object}
     */
    static combine(scope, data, state) {
        let combinedState = {};
        combinedState[scope] = {};
        // Use the original data as a base.
        if (state && state[scope]) {
            combinedState[scope] = state[scope];
        }
        // Update all values from the given data.
        for (let key in data) {
            combinedState[scope][key] = data[key];
        }
        return combinedState;
    }
}

export default Action;
