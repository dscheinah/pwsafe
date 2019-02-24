import Storage from "../storage.js";

const request = async function(path, query, method, body) {
	let response = await fetch(`/${path}${query}`, {
		method: method,
		body: body
	});
	return response.json();
}, querystring = function(...args) {
	let query = new URLSearchParams('');
	args.forEach(function(data) {
		for (var key in data) {
			query.append(key, data[key]);
		}
	});
	let querystring = query.toString();
	if (querystring) {
		querystring = `?${querystring}`;
	}
	return querystring;
};

class Backend extends Storage {
	async load(key, params) {
		return request(key, querystring(this.data, params || {}), 'get');
	}

	async save(key, form) {
		return request(key, querystring(this.data), 'post', new FormData(form));
	}

	async remove(key, params) {
		return request(key, querystring(this.data, params || {}), 'delete');
	}
}

export default Backend;
