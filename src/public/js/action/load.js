import Action from "../lib/action.js";
import Backend from "../lib/storage/backend.js";
import Page from "../lib/page.js";

class Load extends Action {
	constructor(target, backend, page) {
		super();
		this.target = target;
		this.backend = backend;
		this.page = page;
	}

	async convert(trigger) {
		if (trigger && trigger.dataset.id) {
			return this.backend.load(this.target, {id: trigger.dataset.id});
		}
		return this.backend.load(this.target);
	}

	reduce(state, payload) {
		let result = {};
		result[this.target] = payload;
		return result;
	}

	run(payload) {
		this.page.enable();
	}
}

export default Load;
