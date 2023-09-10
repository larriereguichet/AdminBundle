import {Controller} from "@hotwired/stimulus";
import {Modal} from "bootstrap";

export default class extends Controller {
    openModal(event) {
        event.preventDefault()

        const modal = new Modal(this.element.dataset.target)
        modal.show()
    }
};
