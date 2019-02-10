import Action from "../lib/action.js";
import Navigation from "../lib/navigation.js";

class Back extends Action {
	constructor(navigation) {
		super();
		this.navigation = navigation;
	}

	reduce(state, payload) {
		this.navigation.close();
		return {};
	}
}

export default Back;
