import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        const selector = this.element.dataset.target

        if (!selector) {
            return
        }
        const target = document.querySelector(selector)

        if (!target) {
            return
        }
        this.element.addEventListener('click', event => {
            event.preventDefault()
            target.classList.add('collapse')
            target.classList.toggle('show')

            if (target.classList.contains('show')) {
                target.textContent = target.dataset.collapsedMessage
            } else {
                target.textContent = target.dataset.collapseMessage
            }
        })
    }
};
