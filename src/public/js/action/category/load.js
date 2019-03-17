import Parent from '../load.js';

/**
 * The action to load category data by ID to fill the edit template.
 */
class Load extends Parent {

    /**
     * Creates the action using a fixed scope.
     *
     * @param {Page}    page
     * @param {Backend} backend
     */
    constructor(page, backend) {
        super(page, 'category', backend);
    }

    /**
     * Loads the data using the value of the trigger button as ID.
     *
     * @param {HTMLButtonElement} trigger
     *
     * @returns {Promise<{Object}>}
     */
    async convert(trigger) {
        return this.backend.run(this.backend.load, 'category', {id: trigger.value});
    }
}

export default Load;