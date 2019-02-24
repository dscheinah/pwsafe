import Template from "../lib/template.js";

const cache = {}, ids = function(index) {
	if (!cache[this.key]) {
		cache[this.key] = Math.random().toString(36).substr(2, 5);
	}
	return `id_${cache[this.key]}_${index}`;
}

export default ids;
