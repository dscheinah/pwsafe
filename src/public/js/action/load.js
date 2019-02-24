import Open from "./open.js";
import Backend from "../lib/storage/backend.js";

class Load extends Open {
	constructor(page, target, backend) {
		super(page);
		this.target = target;
		this.backend = backend;
	}

	async convert(trigger) {
		if (trigger && trigger.value) {
			return this.backend.load(this.target, {id: trigger.value});
		}
		return this.backend.load(this.target);
	}

	reduce(state, payload) {
		payload.visible = false;
		state[this.target] = payload;
		return state;
	}
}

export default Load;
