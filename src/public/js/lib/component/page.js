import Component from "../component.js";
import Navigation from "../navigation.js";
import Template from "../template.js";

class Page extends Component {
	constructor(navigation, template) {
		super();
	}

	show() {

	}

	enable() {
		this.navigation.open(this);
	}
}

export default Page;
