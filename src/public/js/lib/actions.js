import Action from "./action.js";
import State from "./state.js";

const actionFromTarget = function(target, tag) {
	do {
		if (tag && target.tagName.toLowerCase() !== tag) {
			continue;
		}
		let action = target.dataset.action;
		if (action) {
			return action;
		}
	} while ((target = target.parentNode) && target instanceof Element);
	return '';
}

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

	listen(event, tag) {
		if (tag) {
			tag = tag.toLowerCase();
		}
		this.container.addEventListener(event, e => {
			if (!e.target) {
				return;
			}
			let action = actionFromTarget(e.target, tag);
			if (!action || !this.actions[action]) {
				return;
			}
			this.trigger(action, e.target);
			e.preventDefault();
		});
	}

	trigger(key, trigger) {
		if (!this.actions[key]) {
			return;
		}
		this.actions[key].forEach(action => {
			 action.convert(trigger).then(payload => {
				 this.state.dispatch(action, payload);
				 action.run(payload);
			 });
		});
	}
}

export default Actions;
