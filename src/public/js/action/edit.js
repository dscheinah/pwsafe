import Open from "./open.js";

class Edit extends Open {
	constructor(page, source, target) {
		super(page);
		this.source = source;
		this.target = target;
	}

	reduce(state, payload) {
		let data = state[this.source];
		data.password = '';
		state[this.target] = data;
		return state;
	}
}

export default Edit;
