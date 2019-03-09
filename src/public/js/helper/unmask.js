/**
 * Provides a helper for templates to print a unmasked password with equal spacing by css.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const unmask = function (value) {
    return value.split('').map((c) => `<span class="hidden-password">${c}</span>`).join('');
};

export default unmask;
