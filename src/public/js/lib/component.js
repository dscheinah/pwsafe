import Part from './part.js';

/**
 * Represents a basic component with parts and the update method needed by the application state.
 * It must be extended to represent a meaningful component.
 */
class Component {

    /**
     * Creates a new component with the possibility to add parts.
     */
    constructor() {
        this.parts = {};
    }

    /**
     * Adds a new part with the given key. A part will be updated if the key is present in the data to update.
     *
     * @param {string} key
     * @param {Part}   part
     */
    part(key, part) {
        if (!(part instanceof Part)) {
            throw new TypeError('part must be instanceof Part');
        }
        this.parts[key] = part;
    }

    /**
     * Must be implemented to update the components data from the application state. It is called by the application
     * state each time the data has possibly changed.
     * To handle parts this basic implementation searches for the key of the part present in the data and forwards
     * the value of this key to the parts update method.
     * The scope is given by the application state to hint the component of it's registration.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        for (let key in this.parts) {
            let value = data[key];
            if (value) {
                this.parts[key].update(value, data);
            }
        }
    }
}

export default Component;
