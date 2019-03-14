import Template from './template.js';

/**
 * Defines the methods all parts must implement to be usable by components.
 */
class Part {

    /**
     * Creates a new part with the given template. Parts are assumed to be used for rendering.
     *
     * @param {Template} template
     */
    constructor(template) {
        if (!(template instanceof Template)) {
            throw new TypeError('template must be instanceof Template');
        }
        this.template = template;
    }

    /**
     * Must be implemented to implement logic for complex parts.
     * In this simple implementation the data is simply forwarded to the template.
     * The parent components data is also given to allow access to the parents data scope.
     *
     * @param {(Object|*)} data
     * @param {Object}     parent
     */
    update(data, parent) {
        this.template.set(data);
    }

    /**
     * This method is called when a part is inserted into a template. The container is the position inside the parent
     * template where it should be rendered.
     * Usually applying the new container to the template should be the correct way to handle this.
     *
     * @param {Element} container
     */
    render(container) {
        this.template.render(container);
    }
}

export default Part;
