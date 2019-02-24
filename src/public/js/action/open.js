import Action from "../lib/action.js";
import Page from "../lib/page.js";

class Open extends Action {
	constructor(page) {
		super();
		this.page = page;
	}

	run(payload) {
		this.page.enable();
	}
}

export default Open;
