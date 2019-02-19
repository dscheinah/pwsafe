import Action from "../lib/action.js";

class Init extends Action {
	constructor(actions, login) {
		super();
		this.actions = actions;
		this.login = login;
	}

	async convert(trigger) {
		return trigger.load('login');
	}

	reduce(state, payload) {
		return {
			login: {
				login: payload ? payload.login : '' || '',
			}
		};
	}

	run(payload) {
		this.login.enable();
	}
}

export default Init;
