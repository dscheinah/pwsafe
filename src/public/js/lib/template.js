import Part from "./part.js";

const evaluate = function(string, keys, values) {
	try {
		let result = new Function(...keys, `return \`${string}\`;`)(...values).trim();
		if (result === 'false') {
			return '';
		}
		return result;
	} catch (e) {
		console.error(e);
		return '';
	}
}, nodeFromTemplate = function(template, type) {
	if (template.dataset.rendered) {
		return template.previousSibling;
	}
	let node = document.createElement(template.dataset.tag || 'div');
	node.classList.add(type);
	node.dataset.template = type;
	template.parentNode.insertBefore(node, template);
	template.dataset.rendered = true;
	return node;
}, isPart = function(template, parent) {
	if (template.dataset.part) {
		return true;
	}
	while ((template = template.parentNode) && template !== parent) {
		if (template.dataset.template === 'part') {
			return true;
		}
	}
	return false;
}

class Template {
	constructor(key, parent) {
		this.key = key;
		this.parent = parent;
	}

	async load() {
		if (this.container) {
			return;
		}
		let response = await fetch(`/templates/${this.key}.html`);
		this.container = document.createElement('div');
		this.container.classList.add('page');
		this.container.classList.add(this.key);
		this.container.innerHTML = await response.text();
	}

	set(data) {
		let keys = Object.keys(data), values = Object.values(data);
		this.container.querySelectorAll('template').forEach(template => {
			if (isPart(template, this.parent)) {
				return;
			}
			let condition = template.dataset.condition;
			if (condition && !evaluate(condition, keys, values)) {
				return;
			}
			let newHtml = evaluate(template.innerHTML, keys, values), node = nodeFromTemplate(template, 'template');
			if (node.innerHTML !== newHtml) {
				node.innerHTML = newHtml;
			}
		});
	}

	render(parent) {
		if (parent) {
			this.parent = parent;
		}
		this.parent.appendChild(this.container);
	}

	insert(key, part) {
		let template = this.container.querySelector(`template[data-part=${key}]`);
		if (template) {
			part.render(nodeFromTemplate(template, 'part'));
		}
	}
}

export default Template;
