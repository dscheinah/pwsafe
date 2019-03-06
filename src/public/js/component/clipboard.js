import Component from '../lib/component.js';

/**
 * Defines a simple non rendered component to copy data into the clipboard.
 * It must be registered to the application state to listen on updates for the scope to copy a value from.
 * There needs to be an action to trigger the change of the according scope.
 */
class Clipboard extends Component {

    /**
     * Copies the value property of the updated scope into the clipboard by injecting a temporary input into the DOM.
     *
     * @param {Object} data
     * @param {string} scope
     */
    update(data, scope) {
        let input = document.createElement('input');
        // The element needs to be injected into the DOM to be selectable.
        // Only selected content can be copied to the clipboard.
        document.body.appendChild(input);
        input.value = data.value;
        input.select();
        document.execCommand('copy');
        // After successful copy the element can be removed to keep the DOM clean.
        document.body.removeChild(input);
        // Clear the clipboard after 30s to prevent accidental pasting of passwords.
        setTimeout(() => {
            this.update({value: ''}, scope);
        }, 30000);
    }
}

export default Clipboard;
