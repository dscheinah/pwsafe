import Action from "../lib/action.js";

class Generate extends Action {
	constructor(backend) {
		super();
		this.backend = backend;
	}

	async convert(trigger) {
		return this.backend.save('generate', trigger.form);
	}

	reduce(state, payload) {
		return {
			generate: payload
		};
	}
}

export default Generate;
