/**
 * Provides a helper for templates to mask a value with asterix. Used to hide passwords by default in the detail view.
 * Wrap each symbol to create equal spacing in CSS.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const mask = function (value) {
    return '<span class="hidden-password">*</span>'.repeat(value.length);
};

export default mask;
