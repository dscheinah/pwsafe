import * as Action from './namespace/action.js';
import * as Helper from './namespace/helper.js';
import * as Part from './namespace/part.js';
import * as Storage from './namespace/storage.js';
import Actions from './lib/actions.js';
import Clipboard from './component/clipboard.js';
import Menu from './component/menu.js';
import Navigation from './lib/navigation.js';
import Page from './lib/page.js';
import Reload from './component/reload.js';
import State from './lib/state.js';
import Template from './lib/template.js';

// This represents the global application state.
const state = new State();
// Handles all interactions with the client. Events are handled on window scope only (bubbled).
const actions = new Actions(state, window);
// Represents the browsers history and is used to open pages.
const navigation = new Navigation(history);
// Initialize the storages. One to interact with the PHP backend and one for the localStorage.
const backend = new (Storage.Backend)();
const local = new (Storage.Local)(localStorage, 'main', 1);
// Will be registered to all scopes of the application state which interact with the PHP backend.
// It triggers a page reload on auth error to force relogin.
const reload = new Reload();

// Register the non page components.
// The clipboard has a corresponding action registered later in this file.
state.register('clipboard', new Clipboard());
// Stores the last used user name to suggest it on next login.
state.register('login', local);
// Needs to get the encryption key from the login action. The key is needed to access the the PHP backend.
state.register('user', backend);

// Make the template helpers available to all templates.
Template.add('ids', Helper.ids);
Template.add('mask', Helper.mask);
Template.add('unmask', Helper.unmask);

// Create all pages with the corresponding templates.
// This represents the list of available pages.
const pages = {
    'generate': null,
    'login': null,
    'password': null,
    'password_edit': null,
    'passwords': null,
    'profile': null,
};
// The templates will be rendered inside the container.
const container = document.querySelector('#main');
// The templates need to be loaded asynchronously. To load the promises in parallel, collect them for Promise.all.
const loading = [];
// Create each page with a template matching the key. Also register them to the application state.
Object.keys(pages).forEach(function (key) {
    let template = new Template(key, container);
    loading.push(template.load());
    pages[key] = new Page(navigation, template);
    state.register(key, pages[key]);
    state.register(key, reload);
});
// Create the list part for the password list page.
let template = new Template('passwords_list');
loading.push(template.load());
pages.passwords.part('list', new Part.List(template));

// Create the navigation. This needs a template and a component to be only rendered in logged in state.
let menuTemplate = new Template('menu', document.querySelector('#nav'));
loading.push(menuTemplate.load());
const menu = new Menu(menuTemplate);
// The user scope contains the key if the user is logged in. This is checked in the template.
state.register('user', menu);

// The question for the clients confirmation before a password will be deleted.
let confirmDelete = 'Soll das Passwort gelöscht werden?';

// Register all actions needed in the application. The corresponding triggers can be found in the templates
// as buttons or forms with data-action attributes which values match the keys.
actions.add('copy', new Action.ClipboardCopy('password'));
actions.add('generate', new Action.PasswordGenerate(backend));
actions.add('generate_apply', new Action.Apply('password_edit', navigation));
actions.add('generate_open', new Action.Open(pages.generate));
actions.add('login', new Action.Login(backend, actions));
actions.add('password', new Action.PasswordDetail(pages.password, backend));
actions.add('password_add', new Action.PasswordAdd(pages.password_edit));
actions.add('password_delete', new Action.Delete('passwords', backend, 'password', confirmDelete));
actions.add('password_edit', new Action.Edit(pages.password_edit, 'password', 'password_edit'));
actions.add('password_save', new Action.PasswordSave(navigation, backend));
actions.add('profile', new Action.ProfileEdit(pages.profile));
actions.add('profile_save', new Action.ProfileSave(navigation, backend));
actions.add('show', new Action.PasswordShow());
// These actions are triggered by JavaScript code only. Init after template loading at the end of this file and
// passwords after a successful login.
actions.add('init', new Action.Init(pages.login));
actions.add('passwords', new Action.Load(pages.passwords, 'passwords', backend));

// Start the event listeners. These will trigger the registered actions.
actions.listen('click', 'button');
actions.listen('submit');

// The navigation needs to be started to listen to the popstate event.
navigation.start(window);

// Load all templates in parallel. If this is finished the init action runs to fill the initial application state
// and open the login page.
Promise.all(loading).then(() => actions.trigger('init', local));
