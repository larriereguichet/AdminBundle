import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('click', (event) => {
            event.preventDefault()
            const targetSelector = this.element.dataset.target
    
            if (!targetSelector) {
                throw new Error('The target element is invalid')
            }
            document
                .querySelectorAll(targetSelector)
                .forEach(target => {
                    target.classList.toggle('d-none')
                })
        })
    }
}
