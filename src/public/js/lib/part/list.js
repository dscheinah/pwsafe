import Part from "../part.js";

class List extends Part {
	constructor(template) {
		super(template);
	}

	update(data) {
		if (!this.container) {
			this.container = this.template.container.cloneNode(true);
		} else {
			this.template.parent.removeChild(this.template.container);
			this.template.container = this.container.cloneNode(true);
			this.template.render();
		}
		data.forEach(entry => {
			this.template.set(entry);
			let templates = this.template.container.querySelectorAll('template[data-list=repeat]');
			templates.forEach(template => template.dataset.rendered = '');
		});
	}
}

export default List;
