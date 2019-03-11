/**
 * Provides a helper for templates to mask a value with asterix. Used to hide passwords by default in the detail view.
 * Wrap each symbol to create equal spacing in CSS.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const mask = function (value) {
    let widthChecker = document.createElement('span'), stars = '*'.repeat(value.length);
    // Create a temporary visible element to measure the real string length.
    widthChecker.innerHTML = value;
    document.body.appendChild(widthChecker);
    let width = widthChecker.offsetWidth;
    document.body.removeChild(widthChecker);
    // Use the measured length to style the target element. This allows equal width on toggling.
    return `<span class="hidden-password" style="width: ${width}px;">${stars}</span>`;
};

export default mask;
