import Action from "../lib/action.js";

class Delete extends Action {
	constructor(key, list, backend, message) {
		super();
		this.key = key;
		this.list = list;
		this.backend = backend;
		this.message = message;
	}

	async convert(trigger) {
		if (confirm(this.message)) {
			return this.backend.remove(this.key, {id: trigger.value});
		}
		return {};
	}

	reduce(state, payload) {
		if (!payload.id) {
			return;
		}
		let undeleteList = [];
		state[this.list].list.forEach((entry) => {
			if (entry.id !== payload.id) {
				undeleteList.push(entry);
			}
		});
		state[this.list].list = undeleteList;
		return state;
	}
}

export default Delete;
