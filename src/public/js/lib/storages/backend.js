import Storages from '../storages.js';

/**
 * Fetches data using the fetch API and expects the response to be JSON evaluation to an object.
 *
 * @param {string} path
 * @param {string} query
 * @param {string} method
 * @param {*=}     body
 *
 * @returns {Promise<{Object}>}
 */
const request = async function (path, query, method, body) {
    let response = await fetch(`/${path}${query}`, {
        method: method,
        body: body,
    });
    return response.json();
};

/**
 * A helper function to create a querystring with leading question mark.
 * It uses the key value pairs from the arguments and combines all given arguments.
 * Array values are not supported. To query array use the array syntax ([]) in the keys manually.
 *
 * @param {Object} args
 *
 * @returns {string}
 */
const querystring = function (...args) {
    // Append all key value pairs to the query.
    let query = new URLSearchParams('');
    args.forEach(function (data) {
        for (let key in data) {
            query.append(key, data[key]);
        }
    });
    // If the query has any parameters prepend the question mark. This allows the request function to use it as is.
    let querystring = query.toString();
    if (querystring) {
        querystring = `?${querystring}`;
    }
    return querystring;
};

/**
 * Implement a basic connection to a PHP backend returning JSON objects.
 */
class Backend extends Storages {

    /**
     * Loads data from the backend. The key is used as the absolute path.
     * The params are combined with the data from the application state (see parent class).
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<{Object}>}
     */
    async load(key, params) {
        return request(key, querystring(this.data, params || {}), 'get');
    }

    /**
     * Saves the form using a FormData object from the given element using the key as an absolute path.
     * The querystring for the POST request is generated from the application state (see parent class).
     *
     * @param {string}          key
     * @param {HTMLFormElement} form
     *
     * @returns {Promise<{Object}>}
     */
    async save(key, form) {
        return request(key, querystring(this.data), 'post', new FormData(form));
    }

    /**
     * Works the same as load but uses the DELETE method.
     *
     * @param {string}  key
     * @param {Object=} params
     *
     * @returns {Promise<{Object}>}
     */
    async remove(key, params) {
        return request(key, querystring(this.data, params || {}), 'delete');
    }
}

export default Backend;
