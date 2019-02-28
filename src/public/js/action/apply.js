import Action from "../lib/action.js";

class Apply extends Action {
	constructor(target, navigation) {
		super();
		this.target = target;
		this.navigation = navigation;
	}

	async convert(trigger) {
		let data = {};
		Array.from(trigger.elements).forEach(element => {
			if (element.name) {
				data[element.name] = element.value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			}
		});
		return data;

	}

	reduce(state, payload) {
		if (!state[this.target]) {
			state[this.target] = {};
		}
		for (var key in payload) {
			state[this.target][key] = payload[key];
		}
		return state;
	}

	run(payload) {
		this.navigation.close();
	}

}

export default Apply;
