import * as Action from './namespace/action.js';
import * as Helper from './namespace/helper.js';
import * as Part from './namespace/part.js';
import * as Storage from './namespace/storage.js';
import Actions from './lib/actions.js';
import Clipboard from './component/clipboard.js';
import Fragment from './component/fragment.js';
import Menu from './component/menu.js';
import Navigation from './lib/navigation.js';
import Page from './lib/page.js';
import Reload from './component/reload.js';
import State from './lib/state.js';
import Template from './lib/template.js';
import Templates from './lib/templates.js';

// Small polyfill for Sailfish not provided by babel.
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}
// Use feature detection to hide copy button if not supported.
try {
    document.execCommand('copy');
} catch {
    document.body.classList.add('no-copy');
}

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
// It triggers a page reload on auth error to force re-login.
const reload = new Reload();
// Create the template manager to be used as a factory.
const templates = new Templates();

// Register the non page components.
// The clipboard has a corresponding action registered later in this file.
state.register('clipboard', new Clipboard());
// Stores the last used user name to suggest it on next login.
state.register('login', local);
// Needs to get the encryption key from the login action. The key is needed to access the the PHP backend.
state.register('current', backend);

// Make the template helpers available to all templates.
Template.add('checkbox', Helper.checkbox);
Template.add('filter', Helper.filter);
Template.add('ids', Helper.ids);
Template.add('mark', Helper.mark);
Template.add('mask', Helper.mask);
Template.add('wbr', Helper.wbr);

// Create all pages with the corresponding templates.
// This represents the list of available pages.
const pages = {
    'category': null,
    'categories': null,
    'generate': null,
    'group': null,
    'groups': null,
    'login': null,
    'password': null,
    'password_edit': null,
    'passwords': null,
    'profile': null,
    'user': null,
    'users': null,
};
// The templates will be rendered inside the container.
const container = document.querySelector('#main');
// Create each page with a template matching the key. Also register them to the application state.
Object.keys(pages).forEach(function (key) {
    pages[key] = new Page(navigation, templates.get(key, container));
    state.register(key, pages[key]);
    state.register(key, reload);
});
// Create the list part for the pages with lists.
['categories', 'groups', 'passwords', 'users'].forEach(function(key) {
    pages[key].part('list', new Part.List(templates.get(key + '_list')));
});

// Use the loaded categories to create a select for password edit and a filter for the list.
pages.password_edit.part('categories', new Part.Select(templates.get('category_select'), 'category_id'));
// Currently this ugly  wrapper is needed to get access to the list property inside the categories scope.
let wrapper = new Part.Basic(templates.get('category_filter'));
wrapper.part('list', new Part.List(templates.get('category_list')));
pages.passwords.part('categories', wrapper);
// The data from this scope will be directly forwarded to the same named part.
state.register('categories', pages.passwords);
state.register('categories', pages.password_edit);

// Register the share templates to the password edit page.
let share = new Part.Basic(templates.get('password_share'));
share.part('groups', new Part.List(templates.get('password_groups')));
share.part('users', new Part.List(templates.get('password_users')));
pages.password_edit.part('share', share);
state.register('share', pages.password_edit);

// Add the role selection to the user edit page.
pages.user.part('roles', new Part.Select(templates.get('user_select'), 'role'));
// Add the group selection to the user edit page.
pages.user.part('groups', new Part.Select(templates.get('user_groups'), 'group'));
state.register('groups', pages.user);

// Create the navigation. This needs a template and a component to be only rendered in logged in state.
const menu = new Menu(templates.get('menu', document.querySelector('#nav')));
// The current scope contains the key if the user is logged in. This is checked in the template.
state.register('current', menu);
// Also register the loading animation within the menu.
menu.part('loading', new Part.Basic(templates.get('loading')));
state.register('loading', menu);

// The question for the clients confirmation before a password will be deleted.
let confirmDelete = 'Soll der Datensatz wirklich gelöscht werden?';

let loadStart = new Action.Loading(true), loadEnd = new Action.Loading(false);
/**
 * Registers an action with additional loading animation to indicate the backend work to the user.
 *
 * @param {string} key
 * @param {Action} action
 */
const loading = function(key, action) {
    actions.add(key, loadStart);
    actions.add(key, action);
    actions.add(key, loadEnd);
};

// Register all actions needed in the application. The corresponding triggers can be found in the templates
// as buttons or forms with data-action attributes which values match the keys.
loading('category', new Action.Load(pages.category, 'category', backend));
actions.add('category_add', new Action.Add(pages.category, 'category', {id: '', name: ''}));
loading('category_delete', new Action.Delete('categories', backend, 'category', confirmDelete));
loading('category_save', new Action.CategorySave(navigation, backend));
loading('categories', new Action.Load(pages.categories, 'categories', backend));
actions.add('close', new Action.Close(navigation));
actions.add('copy', new Action.ClipboardCopy('password'));
loading('filter', new Action.Filter(actions));
loading('generate', new Action.PasswordGenerate(backend));
actions.add('generate_apply', new Action.Apply(navigation, 'password_edit'));
actions.add('generate_copy', new Action.ClipboardCopy('generate', navigation));
actions.add('generate_open', new Action.Generate(pages.generate, true));
actions.add('generate_type', new Action.Change('generate'));
loading('group', new Action.Load(pages.group, 'group', backend));
actions.add('group_add', new Action.Add(pages.group, 'group', {id: '', name: ''}));
loading('group_delete', new Action.Delete('groups', backend, 'group', confirmDelete));
loading('group_save', new Action.Save(navigation, 'group', backend, 'groups'));
loading('groups', new Action.Load(pages.groups, 'groups', backend));
// This actions is triggered at the end of this file after all templates are loaded.
actions.add('init', new Action.Init(pages.login));
loading('login', new Action.Login(backend, actions));
loading('password', new Action.PasswordDetail(pages.password, backend));
loading('password_add', new Action.PasswordAdd(pages.password_edit, backend));
loading('password_delete', new Action.Delete('passwords', backend, 'password', confirmDelete));
loading('password_edit', new Action.PasswordEdit(pages.password_edit, backend));
loading('password_save', new Action.PasswordSave(navigation, backend));
loading('password_search', new Action.Search('passwords', backend));
// The passwords action is triggered from the menu and after successful login.
loading('passwords', new Action.PasswordList(pages.passwords, backend));
actions.add('profile', new Action.ProfileEdit(pages.profile));
actions.add('profile_generate', new Action.Generate(pages.generate, false));
loading('profile_save', new Action.ProfileSave(navigation, backend));
actions.add('show', new Action.PasswordShow());
loading('user', new Action.Load(pages.user, 'user', backend));
actions.add('user_add', new Action.Add(pages.user, 'user', {id: '', user: '', role: ''}));
loading('user_delete', new Action.Delete('users', backend, 'user', confirmDelete));
loading('user_save', new Action.Save(navigation, 'user', backend, 'users'));
loading('users', new Action.Load(pages.users, 'users', backend));
// This is a hack to load the base data for the group selection without implementing a separate user action.
loading('users', new Action.Load(pages.users, 'groups', backend));

// Start the event listeners. These will trigger the registered actions.
actions.listen('click', 'button');
actions.listen('submit');
// Used for category selection on passwords list and the type select in generation.
// Must be scoped to the specific tags to not bubble to the form and trigger the submit action.
actions.listen('change', 'input');
actions.listen('change', 'select');

// The navigation needs to be started to listen to the popstate event.
navigation.start(window);

// Register the automatic search feature. This must listen to categories and passwords to follow the initial setup path.
const fragment = new Fragment(actions);
state.register('categories', fragment);
state.register('passwords', fragment);

// Load all templates in parallel.
// If this is finished the init action runs to fill the initial application state and open the login page.
templates.load().then(() => actions.trigger('init', local)).then().catch(window.onerror);
