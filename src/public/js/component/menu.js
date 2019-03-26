import Component from '../lib/component.js';
import Template from '../lib/template.js';

/**
 * Handles visibility of global menu items. This uses templates in a similar way a page does but does not depend
 * on navigation or direct user interaction to be rendered.
 */
class Menu extends Component {

    /**
     * Creates the menu component with the given template.
     *
     * @param {Template} template
     */
    constructor(template) {
        if (!(template instanceof Template)) {
            throw new TypeError('template must be instanceof Template');
        }
        super();
        this.template = template;
    }

    /**
     * Renders the template and registered parts if the given scope changes. The template can use conditions to toggle
     * visibility of menu entries.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        // If the loading animation is requested, simply forward the data to the part to not kill the rendered menu.
        let loading = this.parts[scope];
        if (loading) {
            loading.update(data);
            this.template.insert(scope, loading);
            return;
        }
        // The super call forwards the data to all registered parts.
        super.update(data, scope);
        this.template.set(data);
        // The parts must be inserted into the template to be rendered.
        for (let sub in this.parts) {
            this.template.insert(sub, this.parts[sub]);
        }
        this.template.render();
    }
}

export default Menu;
