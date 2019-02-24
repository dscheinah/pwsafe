import Component from "../lib/component.js";

class Clipboard extends Component {

	update(data) {
        let input = document.createElement('INPUT');
        document.body.appendChild(input);
        input.value = data.value;
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
	}
}

export default Clipboard;
