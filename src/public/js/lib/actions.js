import Action from "./action.js";
import State from "./state.js";

class Actions {
	constructor(state, container) {
		this.state = state;
		this.container = container;
		this.actions = {};
	}

	add(key, action) {
		if (!this.actions[key]) {
			this.actions[key] = [];
		}
		this.actions[key].push(action);
	}

	listen(event) {
		this.container.addEventListener(event, function(e) {
			if (!e.target) {
				return;
			}
			let id = e.target.id || e.target.dataset.id;
			if (!this.actions[id]) {
				return;
			}
			this.trigger(id, e.target);
			e.preventDefault();
		});
	}

	trigger(key, trigger) {
		if (!this.actions[key]) {
			return;
		}
		this.actions[key].forEach(action => {
			 action.convert(trigger).then(payload => this.state.dispatch(action, payload));
		});
	}
}

export default Actions;
