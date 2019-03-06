/**
 * Provides a helper for templates to mask a value with asterix. Used to hide passwords by default in the detail view.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const mask = function (value) {
    return '*'.repeat(value.length);
};

export default mask;
