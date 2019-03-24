import Template from './template.js';

/**
 * This is a simple template manager to remove boilerplate code from the app.
 */
class Templates {

    /**
     * Creates the template manager.
     */
    constructor() {
        this.loading = [];
    }

    /**
     * Creates a template with the specified key. The templates loading will be prepared to be used with load.
     *
     * @param {string}   key
     * @param {Element=} container
     *
     * @returns {Template}
     */
    get(key, container) {
        let template = new Template(key, container);
        this.loading.push(template.load());
        return template;
    }

    /**
     * Loads all templates. The returned promise will be resolved once all templates are loaded.
     * This allows parallel loading of all templates.
     *
     * @return {Promise<{void}>}
     */
    async load() {
        return Promise.all(this.loading);
    }
}

export default Templates;