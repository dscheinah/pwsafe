import Template from "./template.js";

class Part {
	constructor(template) {
		this.template = template;
	}

	update(data) {
		this.template.set(data);
	}
}

export default Part;
