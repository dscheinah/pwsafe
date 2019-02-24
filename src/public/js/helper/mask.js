import Template from "../lib/template.js";

const mask = function(value) {
	return '*'.repeat(value.length);
}

export default mask;
