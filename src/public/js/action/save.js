import Action from "../lib/action.js";

class Save extends Action {
	constructor(form, card, list, backend, navigation) {
		super();
		this.form = form;
		this.card = card;
		this.list = list;
		this.backend = backend;
		this.navigation = navigation;
	}

	run(payload) {
		this.navigation.close();
	}
}

export default Save;
