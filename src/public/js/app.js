import * as Action from "./namespace/action.js";
import * as Component from "./namespace/component.js";
import * as Part from "./namespace/part.js";
import * as Storage from "./namespace/storage.js";
import Actions from "./lib/actions.js";
import Navigation from "./lib/navigation.js";
import State from "./lib/state.js";
import Template from "./lib/template.js";

const state = new State();
const actions = new Actions(state, window);
const navigation = new Navigation(history);
const backend = new Storage.Backend();

const container = document.querySelector('#main');
const pages = {};

['passwords', 'password'].forEach(function(key) {
	pages[key] = new Component.Page(navigation, new Template(key, container));
	state.register(key, pages[key]);
});
['passwords'].forEach(function(key) {
	pages[key].part('list', new Part.List(new Template(key + '-list')));
})

actions.add('passwords', new Action.Load('passwords', backend, pages.passwords));
actions.add('password', new Action.Load('password', backend, pages.password));
actions.add('close', new Action.Back(navigation));

actions.listen('click');

actions.trigger('passwords');

navigation.start(window);
