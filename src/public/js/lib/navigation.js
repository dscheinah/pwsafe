import Page from "./component/page.js";

class Navigation {
	constructor(history) {
		this.history = history;
		this.states = {};
		this.id = 0;
	}

	start(window) {
		window.addEventListener('popstate', e => {
			let id = e.state;
			if (this.states[id]) {
				this.states[id].show();
				this.id = id;
			}
		});
	}

	open(page) {
		this.id++;
		this.states[this.id] = page;
		if (Object.keys(this.states).length) {
			this.history.pushState(this.id, '', window.location.href);
		} else {
			this.history.replaceState(this.id, '', window.location.href);
		}
		page.show();
	}

	close() {
		this.history.go(-1);
	}
}

export default Navigation;
