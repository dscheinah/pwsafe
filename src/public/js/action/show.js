import Action from "../lib/action.js";

class Show extends Action {
	constructor(target) {
		super();
		this.target = target;
	}

	reduce(state, payload) {
		state[this.target].visible = !state[this.target].visible
		return state;
	}
}

export default Show;
