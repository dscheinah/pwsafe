import Action from "../lib/action.js";

class Login extends Action {
	constructor(backend, actions) {
		super();
		this.backend = backend;
		this.actions = actions;
	}

	async convert(trigger) {
		trigger.elements[1].value = '';
		return {
			login: trigger.elements[0].value,
		}
	}

	reduce(state, payload) {
		return {
			login: {
				login: payload.login,
			}
		}
	}

	run(payload) {
		this.actions.trigger('passwords');
	}
}

export default Login;
