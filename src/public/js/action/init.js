import Action from "../lib/action.js";

class Init extends Action {
	constructor(login) {
		super();
		this.login = login;
	}

	async convert(trigger) {
		return trigger.load('login');
	}

	reduce(state, payload) {
		if (!payload.user) {
			payload.user = '';
		}
		return {
			login: payload,
			generate: {}
		};
	}

	run(payload) {
		this.login.enable();
	}
}

export default Init;
