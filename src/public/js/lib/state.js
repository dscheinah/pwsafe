import Action from "./action.js";
import Component from "./component.js";

class State {
	constructor() {
		this.state = {};
		this.components = {};
	}

	register(key, component) {
		if (!this.components[key]) {
			this.components[key] = [];
		}
		this.components[key].push(component);
		if (this.state[key]) {
			component.update(this.state[key], key);
		}
	}

	dispatch(action, payload) {
		var state = action.reduce(this.state, payload);
		for (var key in state) {
			let data = this.state[key] = state[key];
			if (this.components[key]) {
				this.components[key].forEach(component => component.update(data, key));
			}
		}
	}
}

export default State;
