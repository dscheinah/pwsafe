import * as Action from "./namespace/action.js";
import * as Part from "./namespace/part.js";
import * as Storage from "./namespace/storage.js";
import Actions from "./lib/actions.js";
import Navigation from "./lib/navigation.js";
import Page from "./lib/page.js";
import State from "./lib/state.js";
import Template from "./lib/template.js";

const state = new State();
const actions = new Actions(state, window);
const navigation = new Navigation(history);
const backend = new Storage.Backend();
const local = new Storage.Local(localStorage, 'main', 1);

state.register('user', backend);
state.register('login', local);

const container = document.querySelector('#main');
const pages = {}, loading = [];

[
	'login',
	'password',
	'password-edit',
	'passwords',
].forEach(function(key) {
	let template = new Template(key, container);
	loading.push(template.load());
	pages[key] = new Page(navigation, template);
	state.register(key, pages[key]);
});
let template = new Template('passwords-list');
loading.push(template.load());
pages.passwords.part('list', new Part.List(template));

actions.add('init', new Action.Init(actions, pages.login));
actions.add('login', new Action.Login(backend, actions));
actions.add('password', new Action.Load('password', backend, pages.password));
actions.add('password-edit', new Action.Edit('password', 'password-edit', pages['password-edit']));
actions.add('password-save', new Action.Save('password-edit', 'password', 'password-list', backend, navigation));
actions.add('passwords', new Action.Load('passwords', backend, pages.passwords));

actions.listen('click', 'button');
actions.listen('submit');

navigation.start(window);

Promise.all(loading).then(() => {
	actions.trigger('init', local);
});
