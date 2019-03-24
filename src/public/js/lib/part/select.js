import Part from '../part.js';
import Template from '../template.js';

/**
 * A part to render a select from a list.
 * This component is not stateless as it's designed to take the list and the selected option from different scopes.
 * To use it, the page containing the part must listen to changes on the list.
 */
class Select extends Part {

    /**
     * Create the part with the key inside the parent scope which marks the selected value.
     *
     * @param {Template} template
     * @param {string}   key
     */
    constructor(template, key) {
        super(template);
        this.key = key;
    }

    /**
     * Renders the select with the options available as a template helper called options.
     * It takes the list from the value to build the options by id and name.
     * If an id matches the given key inside the parent scope the option is marked as selected.
     *
     * @param {Object}  data
     * @param {Object=} parent
     */
    update(data, parent) {
        if (data.list) {
            this.list = data.list;
        }
        if (parent) {
            this.value = parent[this.key];
        }
        // If no list is available the select is not yet ready to render.
        if (!this.list) {
            return;
        }
        // Add the options as a helper to disable escaping.
        Template.add('options', () => {
            let options = '';
            this.list.forEach((entry) => {
                let attributes = `value="${entry.id}"`;
                if (entry.id === this.value) {
                    attributes += ' selected';
                }
                options += `<option ${attributes}>${entry.name}</option>`;
            });
            return options;
        });
        super.update(data, parent);
    }
}

export default Select;