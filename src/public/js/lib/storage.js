import Component from "./component.js";

class Storage extends Component {
	constructor() {
		super();
		this.data = {};
	}

	update(data) {
		this.data = data;
	}

	async load(key, params) {
		return {};
	}

	async save(key, form) {
		return {};
	}
}

export default Storage;
