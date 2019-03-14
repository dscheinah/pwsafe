import Component from './component.js';
import Navigation from './navigation.js';
import Template from './template.js';

/**
 * Represents a page to be rendered and used in client navigation.
 */
class Page extends Component {

    /**
     * Creates a new page, linking it to the navigation and providing a template.
     *
     * @param {Navigation} navigation
     * @param {Template} template
     */
    constructor(navigation, template) {
        if (!(navigation instanceof Navigation)) {
            throw new TypeError('navigation must be instanceof Navigation');
        }
        if (!(template instanceof Template)) {
            throw new TypeError('template must be instanceof Template');
        }
        super();
        this.navigation = navigation;
        this.template = template;
    }

    /**
     * Renders the template and the parts of this component.
     * This will not trigger any history changes and should therefore only by used internally.
     */
    show() {
        for (let key in this.parts) {
            this.template.insert(key, this.parts[key]);
        }
        this.template.render();
    }

    /**
     * Enable the page using the navigation. This is the preferred method of showing a page.
     * The navigation will trigger the show method of the page after registering the change to the history.
     */
    enable() {
        this.navigation.open(this);
    }

    /**
     * Gives the data to the template to trigger re-rendering.
     * If the scope matches the key of an added part, only the part will be updated.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        // To allow partial updates not providing the complete data.
        let part = this.parts[scope];
        if (part) {
            part.update(data);
            return;
        }
        // The super call will update the data to the parts.
        super.update(data, scope);
        this.template.set(data);
    }
}

export default Page;
