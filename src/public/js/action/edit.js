import Action from "../lib/action.js";

class Edit extends Action {
	constructor(source, target, page) {
		super();
		this.source = source;
		this.target = target;
		this.page = page;
	}

	reduce(state, payload) {
		state[this.target] = state[this.source];
		return state;
	}

	run(payload) {
		this.page.enable();
	}
}

export default Edit;
