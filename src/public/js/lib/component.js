import Part from "./part.js";

class Component {
	constructor() {
		this.parts = {};
	}

	part(key, part) {
		this.parts[key] = part;
	}

	update(data, key) {
		for (var partKey in this.parts) {
			let scope = data[partKey];
			if (scope) {
				this.parts[partKey].update(scope);
			}
		}
	}
}

export default Component;
