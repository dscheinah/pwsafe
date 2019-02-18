import Storage from "../storage.js";

class Backend extends Storage {
	async load(key, params) {
		let querystring = '', keys = Object.keys(params || {});
		if (keys.length) {
			querystring = '?' + keys.map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`).join('&');
		}
		let response = await fetch(`/${key}${querystring}`);
		return response.json();
	}
}

export default Backend;
