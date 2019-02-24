import Component from "../lib/component.js";

class Menu extends Component {
	constructor(template) {
		super();
		this.template = template;
	}

	update(data, key) {
		super.update(data);
		this.template.set(data);
		for (var key in this.parts) {
			this.template.insert(key, this.parts[key]);
		}
		this.template.render();
	}
}

export default Menu;
