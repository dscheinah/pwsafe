import Template from "./template.js";

class Part {
	constructor(template) {
		this.template = template;
	}

	update(data) {
		this.template.set(data);
	}

	render(container) {
		this.template.render(container);
	}
}

export default Part;
