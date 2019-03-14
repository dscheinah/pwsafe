import Part from '../part.js';

/**
 * This is a special part implementation for pages to handle repeated template rendering for lists.
 * It expects the data to be an array.
 */
class List extends Part {

    /**
     * Renders the given list by rendering the template for each entry.
     * The template must contain at least one template element with data-list=repeat attribute. This one will be
     * rendered repeatedly for each entry. Every other setup will probably lead to ugly result.
     *
     * @param {Array}  data
     * @param {Object} parent
     */
    update(data, parent) {
        if (!this.container) {
            // Store the templates container at first update to be able to restore it for subsequent updates.
            this.container = this.template.container.cloneNode(true);
        } else {
            // The original template must be restored and inserted into the DOM instead of the old container.
            // There currently is no other way to empty the previously rendered list.
            this.template.parent.removeChild(this.template.container);
            this.template.container = this.container.cloneNode(true);
            this.template.render();
        }
        // Iterate all entries and render the template.
        data.forEach(entry => {
            this.template.set(Object.assign(parent, entry));
            // By removing the rendered attribute from the elements, the content will be injected again on the next
            // iteration. This will result in a repeatedly rendered content from this templates.
            let templates = this.template.container.querySelectorAll('template[data-list=repeat]');
            templates.forEach((template) => delete template.dataset.rendered);
        });
    }
}

export default List;
