/**
 * Provides a helper for templates to mask a value with asterisk. Used to hide passwords by default in the detail view.
 * Wrap each symbol to create equal spacing in CSS.
 *
 * @param {string} value
 *
 * @returns {string}
 */
const mask = function (value) {
    let widthChecker = document.createElement('span');
    // Create a temporary visible element to measure the real string length.
    widthChecker.innerHTML = value;
    document.body.appendChild(widthChecker);
    let width = widthChecker.getBoundingClientRect().width;
    document.body.removeChild(widthChecker);
    // Fill the available space with stars but allow line-breaks on mobile devices.
    let stars = '★<wbr>'.repeat(width / 13);
    // Use the measured length to style the target element. This allows equal width on toggling.
    return `<span class="hidden-password" style="width: ${width}px;">${stars}</span>`;
};

export default mask;
