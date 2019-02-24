import Edit from "./edit.js";

class Add extends Edit {
	reduce(state, payload) {
		let data = super.reduce(state, payload);
		['name', 'url', 'notice'].forEach((key) => {
			data[this.target][key] = '';
		});
		return data;
	}
}

export default Add;
