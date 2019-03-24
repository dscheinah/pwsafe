/**
 * Creates a radio to be used in the filter scripts.
 * This applies the checked attribute if the id matches the given checked.
 *
 * @param {string|int} id
 * @param {string|int} checked
 *
 * @returns {string}
 */
const filter = function(id, checked) {
    let attributes = `type="radio" name="category_id" data-action="filter" value="${id}"`;
    if (id.toString() === checked.toString()) {
        attributes += ' checked';
    }
    return `<input ${attributes}/>`;
};

export default filter;