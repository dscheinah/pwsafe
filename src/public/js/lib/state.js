import Session from "./session.js";

var State = function(session, stored) {
    this.session = session;
    this.stored = stored || [];
    this.state = {};
    this.components = {};
}
State.prototype.register = function(key, component) {
    if (!this.components[key]) {
	this.components[key] = [];
    }
    this.components[key].push(component);
    var data = this.state[key];
    if (!data) {
	if (this.stored.includes(key)) {
	    data = this.session.retrieve(key) || {};
	} else {
	    data = {};
	}
    }
    this.update(key, data);
}
State.prototype.dispatch = function(action) {
    var state = action.reduce(JSON.parse(JSON.stringify(this.state)));
    for ( var key in state) {
	this.update(key, state[key]);
    }
}
State.prototype.update = function(key, data) {
    this.state[key] = data;
    if (this.stored.includes(key)) {
	this.session.store(key, data);
    }
    if (this.components[key]) {
	this.components[key].forEach(function(component) {
	    component.update(data);
	});
    }
}

export default State;
