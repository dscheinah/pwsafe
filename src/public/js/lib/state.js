import Action from "./action.js";
import Component from "./component.js";

const clone = function(data) {
	return JSON.parse(JSON.stringify(data));
}

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
			component.update(clone(this.state[key]), key);
		}
	}

	dispatch(action, payload) {
		var state = action.reduce(clone(this.state), payload);
		for (var key in state) {
			this.state[key] = state[key];
			if (this.components[key]) {
				this.components[key].forEach(component => component.update(clone(state[key]), key));
			}
		}
	}
}

export default State;
