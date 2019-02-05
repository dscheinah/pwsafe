import State from "./state.js";

var Actions = function(state, container) {
    this.state = state;
    this.container = container;
    this.actions = {};
}
Actions.prototype.add = function(key, action) {
    this.actions[key] = action;
}
Actions.prototype.listen = function(event) {
    this.container.addEventListener(event, function(e) {
	if (!e.target) {
	    return;
	}
	var id = e.target.id || e.target.dataset.id;
	if (!this.actions[id]) {
	    return;
	}
	e.preventDefault();
	this.trigger(id, e.target);
    }.bind(this));
}
Actions.prototype.trigger = function(key, target) {
    if (this.actions[key]) {
	var action = this.actions[key];
	action.set(target);
	this.state.dispatch(action);
    }
}

export default Actions;
