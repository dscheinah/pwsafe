import Storage from "../storage.js";

const request = async function(path, query, body) {
	let response = await fetch(`/${path}${query}`, {
		method: body ? 'post' : 'get',
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
		return request(key, querystring(this.data, params || {}));
	}

	async save(key, form) {
		return request(key, querystring(this.data), new FormData(form));
	}
}

export default Backend;
