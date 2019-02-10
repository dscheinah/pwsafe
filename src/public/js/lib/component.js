import Part from "./part.js";

class Component {
	constructor() {
		this.parts = {};
	}

	part(key, part) {
		this.parts[key] = part;
	}

	update(data) {
		for (var key in this.parts) {
			let scope = data[key];
			if (scope) {
				this.parts[key].update(scope);
			}
		}
	}
}

export default Component;
