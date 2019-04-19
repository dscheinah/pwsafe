import ids from './ids.js';

/**
 * Creates a checkbox to be used in the share parts.
 * This applies the checked attribute if the id matches the given checked.
 *
 * @param {string} type
 * @param {int}    id
 * @param {Array}  checked
 * @param {string} label
 *
 * @returns {string}
 */
const checkbox = function(type, id, checked, label) {
    let attributes = `type="checkbox" id="${ids.call(this, type + id)}" name="${type}[]" value="${id}"`;
    if (checked.includes(id)) {
        attributes += ' checked';
    }
    return `<input ${attributes}/><label for="${ids.call(this, type + id)}">${label}</label>`;
};

export default checkbox;
