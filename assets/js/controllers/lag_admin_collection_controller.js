import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        const removeButtonSelector = this.element.dataset.removeButton

        this.element.querySelectorAll(removeButtonSelector).forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault()
            })
        })
    }

    disconnect() {

    }

    remove(event) {
        event.preventDefault()
        event.currentTarget.parentElement.parentElement.remove()
    }
};
