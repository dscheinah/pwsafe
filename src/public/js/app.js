import * as Action from "./namespace/action.js";
import * as Helper from "./namespace/helper.js";
import * as Part from "./namespace/part.js";
import * as Storage from "./namespace/storage.js";
import Actions from "./lib/actions.js";
import Clipboard from "./component/clipboard.js";
import Navigation from "./lib/navigation.js";
import Page from "./lib/page.js";
import State from "./lib/state.js";
import Template from "./lib/template.js";

const state = new State();
const actions = new Actions(state, window);
const navigation = new Navigation(history);
const backend = new Storage.Backend();
const local = new Storage.Local(localStorage, 'main', 1);

state.register('clipboard', new Clipboard());
state.register('login', local);
state.register('user', backend);

const container = document.querySelector('#main');
const pages = {}, loading = [];

[
	'generate',
	'login',
	'password',
	'password_edit',
	'passwords'
].forEach(function(key) {
	let template = new Template(key, container);
	loading.push(template.load());
	pages[key] = new Page(navigation, template);
	state.register(key, pages[key]);
});
let template = new Template('passwords_list');
loading.push(template.load());
pages.passwords.part('list', new Part.List(template));

for (var helper in Helper) {
	Template.add(helper, Helper[helper]);
}

actions.add('copy', new Action.Copy('password'));
actions.add('generate', new Action.Generate(backend));
actions.add('generate_apply', new Action.Apply('password_edit', navigation));
actions.add('generate_open', new Action.Open(pages.generate));
actions.add('init', new Action.Init(pages.login));
actions.add('login', new Action.Login(backend, actions));
actions.add('password', new Action.Load(pages.password, 'password', backend));
actions.add('password_add', new Action.Add(pages.password_edit, 'login', 'password_edit'));
actions.add('password_edit', new Action.Edit(pages.password_edit, 'password', 'password_edit'));
actions.add('password_save', new Action.Save('password', navigation, backend, 'passwords'));
actions.add('passwords', new Action.Load(pages.passwords, 'passwords', backend));
actions.add('show', new Action.Show('password'));

actions.listen('click', 'button');
actions.listen('submit');

navigation.start(window);

Promise.all(loading).then(() => {
	actions.trigger('init', local);
});
