/**
 * Allows breaking inside URLs or passwords.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const wbr = function(value) {
    if (!value) {
        return '';
    }
    // Add wbr tag to allow breaking on every symbol. Skip ampersand and semicolon to not split html entities.
    return value.replace(/([^\w\d&;])/g, '$1<wbr>');
};

export default wbr;
