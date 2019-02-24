import Action from "../lib/action.js";

class Copy extends Action{
	constructor(source) {
		super();
		this.source = source;
	}

	async convert(trigger) {
		return {key: trigger.value};
	}

	reduce(state, payload) {
		return {
			clipboard: {
				value: state[this.source][payload.key]
			}
		};
	}
}

export default Copy;
