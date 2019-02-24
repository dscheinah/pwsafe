import Action from "../lib/action.js";

class Login extends Action {
	constructor(backend, actions) {
		super();
		this.backend = backend;
		this.actions = actions;
	}

	async convert(trigger) {
		var data = await this.backend.save('login', trigger);
		trigger.elements.password.value = '';
		return data;
	}

	reduce(state, payload) {
		if (!state.user) {
			state.user = {};
		}
		state.user.key = payload.key;
		delete payload.key;
		return {
			login: payload,
			user: state.user
		};
	}

	run(payload) {
		this.actions.trigger('passwords');
	}
}

export default Login;
