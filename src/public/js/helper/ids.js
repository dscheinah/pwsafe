/**
 * Caches the pseudo random ID part in dependency of the templates key.
 *
 * @type {Object}
 */
const cache = {};

/**
 * Provides a template helper to create pseudo random IDs. The IDs will be cached for each template to allow reusing.
 * The function is called in the context of the calling template and use its key for caching.
 * So for the same index this helper returns the same value if called inside the same template.
 *
 * @param {(string|int)} index
 *
 * @this {Template}
 *
 * @returns {string}
 */
const ids = function (index) {
    // Create or reuse the pseudo random key for the calling template. This way a template will always reuse IDs.
    if (!cache[this.key]) {
        // This creates random printable symbols out of the random value.
        cache[this.key] = Math.random().toString(36).substr(2, 5);
    }
    // Combine the random part with the given index and use a prefix to ensure a valid ID attribute.
    return `id_${cache[this.key]}_${index}`;
};

export default ids;
