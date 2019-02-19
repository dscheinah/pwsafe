import Storage from "../storage.js";

class Local extends Storage {
	constructor(storage, prefix, version) {
		super();
		this.storage = storage;
		this.prefix = prefix;
		this.version = version;
	}

	update(data, key) {
		this.storage.setItem([this.prefix, key, this.version].join('-'), JSON.stringify(data));
	}

	async load(key, params) {
		return JSON.parse(this.storage.getItem([this.prefix, key, this.version].join('-')));
	}
}

export default Local;
