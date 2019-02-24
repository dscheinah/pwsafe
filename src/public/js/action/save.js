import Apply from "./apply.js";

class Save extends Apply {
	constructor(target, navigation, backend, list) {
		super(target, navigation);
		this.backend = backend;
		this.list = list;
	}

	async convert(trigger) {
		return this.backend.save(this.target, trigger);
	}

	reduce(state, payload) {
		let data = super.reduce(state, payload), updated = false;
		state[this.list].list.forEach((entry, key) => {
			if (entry.id === payload.id) {
				state[this.list].list[key] = payload;
				updated = true;
			}
		})
		if (!updated) {
			state[this.list].list.push(payload);
		}
		data[this.list] = state[this.list];
		return data;
	}
}

export default Save;
