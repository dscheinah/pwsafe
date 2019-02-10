import Action from "../lib/action.js";
import Backend from "../lib/storage/backend.js";
import Page from "../lib/component/page.js";

class Load extends Action {
	constructor(target, backend, page) {
		super();
		this.target = target;
		this.backend = backend;
		this.page = page;
	}

	convert(trigger) {
		if (trigger) {
			return this.backend.load(this.target, trigger.dataset.id);
		}
		return this.backend.load(this.target);
	}

	reduce(state, payload) {
		var result = {};
		result[this.target] = payload;
		this.page.enable();
		return result;
	}
}

export default Load;
