import Actions from "./lib/actions.js";
import Session from "./lib/session.js";
import State from "./lib/state.js";

(function() {
    var session = new Session('pwsafe', 1);
    var state = new State(session);
    var actions = new Actions(state, window);
})();
