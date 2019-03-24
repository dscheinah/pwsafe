import Part from './part.js';

/**
 * An eval implementation to allow template string syntax inside HTML templates. It evaluates the given string as
 * a template string. The given keys and values are available to be used inside the template.
 *
 * @param {string} string
 * @param {Array}  keys
 * @param {Array}  values
 *
 * @returns {string}
 */
const evaluate = function (string, keys, values) {
    try {
        let result = new Function(...keys, `return \`${string}\`;`)(...values).trim();
        // When used inside conditions a boolean false evaluates to the string 'false'. So return a falsify value.
        if (result === 'false') {
            return '';
        }
        return result;
    } catch (e) {
        // If a key is not found when evaluating the template an exception is thrown due to strict mode.
        // Assume falsify for conditions using undefined keys.
        return '';
    }
};

/**
 * Creates a real element instance from a given template element.
 * If the element is already rendered the previously created element is return. This is indicated by the data-rendered
 * attribute, also set inside this function.
 * The type is used to mark the resulting element for its origin and as a CSS class.
 *
 * @param {HTMLTemplateElement} template
 * @param {string}              type
 *
 * @returns {(Node|Element)}
 */
const nodeFromTemplate = function (template, type) {
    if (template.dataset.rendered) {
        return template.previousSibling;
    }
    let node = document.createElement(template.dataset.tag || 'div');
    let baseStyle = template.getAttribute('class');
    if (baseStyle) {
        node.setAttribute('class', baseStyle);
    }
    node.classList.add(type);
    node.dataset.template = type;
    template.parentNode.insertBefore(node, template);
    template.dataset.rendered = '1';
    return node;
};

/**
 * Checks if the given template element is from this template or a template of a part.
 * The parent is the parent element in which the template is rendered in.
 * This is needed since currently there is no Shadow-DOM isolation for parts hiding the parts templates.
 *
 * @param {HTMLElement} template
 * @param {HTMLElement} parent
 * @param {boolean=}    onlyParent
 *
 * @returns {boolean}
 */
const isPart = function (template, parent, onlyParent) {
    if (!onlyParent && template.dataset.part) {
        return true;
    }
    // If any parent between the given parent and the given template is a rendered result from a part,
    // the template is inside a part and must not contain to this template.
    while (template.parentNode instanceof HTMLElement && (template = template.parentNode) && template !== parent) {
        if (template.dataset.template === 'part') {
            return true;
        }
    }
    return false;
};

/**
 * Collects helper functions from Template.add to be made available to evaluate.
 *
 * @type {Object}
 */
const helper = {

    /**
     * This function escapes all values inside the given array, object or string
     * to be used safely in the context of HTML.
     * It is applied to all values before using evaluate. So there usually is no need to manually call it. It's made
     * available as a helper if needed and to be able to overwrite it.
     *
     * @param {*} values
     *
     * @returns {*}
     */
    escape: function(values) {
        // To be used in context of strings, null is better represented as an empty and therefore falsify string.
        if (values === null) {
            return '';
        }
        if (values instanceof Array) {
            return values.map(helper.escape);
        }
        // The value can be a basic string or a wrapped String-Object. Both can be handled the same.
        if (typeof values === 'string' || values instanceof String) {
            return values.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
        if (values instanceof Object) {
            // Assume a basic key/ value object.
            for (let key in values) {
                values[key] = helper.escape(values[key]);
            }
        }
        return values;
    }
};

/**
 * This class provides template rendering implementing a simple templating engine.The support for repeated rendering
 * from arrays is implemented as a List-part.
 *
 * To use dynamic content from the application state in template, assume the key/ value pairs from the corresponding
 * page or part to be available for JS template string syntax: e.g. <input value="${name}" />
 * Only content of <template></template> elements will be parsed for replacements.
 * To indicate an injection point for a part use: <template data-part="..."></template> with ... being the scope.
 * Also each template can have a data-condition attribute. If it's value evaluates to false or undefined the template
 * will not be rendered for the current data.
 *
 * Be aware that the templates are currently not isolated using the shadow DOM. So avoid using conflicting properties
 * like static IDs.
 */
class Template {

    /**
     * Creates a new template. The key will be used as an unique identifier and to load the template from a file from
     * the templates folder with the corresponding name and .html extension.
     * The parent is the element where the rendered template is injected. It can be omitted for templates of parts.
     *
     * @param {string}   key
     * @param {Element=} parent
     */
    constructor(key, parent) {
        this.key = key;
        this.parent = parent;
    }

    /**
     * This method adds a new helper function to be used in all templates. The name can then be used instead of the
     * function in the template strings of the template.
     *
     * @param {string} name
     * @param {*}      callback
     */
    static add(name, callback) {
        helper[name] = callback;
    }

    /**
     * Loads the template from a file match the key from constructor with the extension .html from the templates folder.
     * The Promise resolves once the loading has finished. If a container is already provided by the constructor or from
     * a previous call to load, nothing is done and the promise immediately resolves.
     *
     * @returns {Promise<void>}
     */
    async load() {
        if (this.container) {
            return;
        }
        this.container = document.createElement('section');
        // Mark the container for CSS to be a page of the type represented by the unique key.
        this.container.classList.add('page');
        this.container.classList.add(this.key);
        // Fill the container with the template. It is still not present in the DOM until render is called.
        // If the build tools created a cache inside the main container, the element will be found by ID.
        let template = document.getElementById(this.key);
        if (template) {
            this.container.innerHTML = template.innerHTML;
        } else {
            let response = await fetch(`/templates/${this.key}.html`);
            this.container.innerHTML = await response.text();
        }
    }

    /**
     * Renders the data into the template by evaluating the template string.
     * Only triggers a redraw if the HTML content has changed. When dealing with form elements this can be tricky!
     *
     * @param {Object} data
     */
    set(data) {
        // Extend the data by the helpers. On duplicate keys the original data is preferred.
        for (let key in helper) {
            if (!data[key]) {
                data[key] = helper[key].bind(this);
            }
        }
        let keys = Object.keys(data), values = Object.values(data);
        // Only apply the templating engine to template elements. This way the template string will not invalidate
        // the HTML code and it saves a bit of performance to reduce the string size to parse.
        this.container.querySelectorAll('template').forEach(
            /** @param {HTMLTemplateElement} template */ (template) => {
                // If the found template is inside a part, the part will do the rendering, not this template.
                if (isPart(template, this.parent)) {
                    return;
                }
                // If there is a condition it must be evaluated.
                let condition = template.dataset.condition, node = nodeFromTemplate(template, 'template');
                if (condition && !evaluate(condition, keys, values)) {
                    // If the condition is not fulfilled, the template is no longer rendered and the maybe previously
                    // rendered content must be removed from DOM.
                    delete template.dataset.rendered;
                    node.parentNode.removeChild(node);
                    return;
                }
                // Evaluate the template string inside the template with the given data and helpers.
                let newHtml = evaluate(template.innerHTML, keys, helper.escape(values));
                // Only replace the content if it has changed to reduce redraws.
                if (node.innerHTML !== newHtml) {
                    node.innerHTML = newHtml;
                }
            }
        );
    }

    /**
     * If the templates should be rendered it only needs to be injected into the DOM. The templating engine runs in set.
     * If a parent is given, it replaces the parent from the constructor. This is usually only used for parts.
     *
     * @param {Element=} parent
     */
    render(parent) {
        if (parent) {
            this.parent = parent;
        }
        this.parent.appendChild(this.container);
    }

    /**
     * Inserts a part into this template by search for the corresponding template with the data-part="key" attribute.
     * If it is found the part will be rendered inside the plain node from this template.
     *
     * @param {string} key
     * @param {Part}   part
     */
    insert(key, part) {
        if (!(part instanceof Part)) {
            throw new TypeError('part must be instanceof Part');
        }
        let templates = this.container.querySelectorAll(`template[data-part=${key}]`);
        templates.forEach(
            /** @param {HTMLTemplateElement} template */ (template) => {
                // Do not apply the part to sub parts of other parts.
                if (isPart(template, this.parent, true)) {
                    return;
                }
                // Use the part type to allow checking for isPart.
                let container = nodeFromTemplate(template, 'part');
                // To be able to style the part by its type.
                container.classList.add(key);
                part.render(container);
        });
    }
}

export default Template;
