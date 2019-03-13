/**
 * Highlights the term inside the value if available by surrounding it with a stylable span.
 * This is not used in an action to not mess with URLs inside the href attribute.
 *
 * @param {string} value
 * @param {string} term
 *
 * @returns {string}
 */
const mark = function(value, term) {
    // Only search for default printable chars to ignore % for MySQL LIKE.
    // With this no symbols not used in MySQL LIKE are highlighted.
    let terms = term.match(/\w+/g);
    // Do no replace if no term is given.
    if (!terms || !terms.length) {
        return value;
    }
    // Simple iteration and global replace may highlight too much substring. But it should be kept simple.
    terms.forEach((term) => {
        // Search insensitive according to the rules of MySQL backend.
        value = value.replace(new RegExp(term, 'ig'), '<span class="highlight">$&</span>')
    });
    return value;
};

export default mark;