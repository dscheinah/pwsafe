import Component from "./component.js";
import Navigation from "./navigation.js";
import Template from "./template.js";

class Page extends Component {
	constructor(navigation, template) {
		super();
		this.navigation = navigation;
		this.template = template;
	}

	show() {
		for (var key in this.parts) {
			this.template.insert(key, this.parts[key]);
		}
		this.template.render();
	}

	enable() {
		this.navigation.open(this);
	}

	update(data, key) {
		super.update(data);
		this.template.set(data);
	}
}

export default Page;
