import {Controller} from '@hotwired/stimulus'
import Quill from 'quill'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'

export default class extends Controller {
    connect() {
        const toolbar = JSON.parse(this.element.dataset.toolbar ?? 'true')
        const textarea = this.element.querySelector('textarea')
        const textContent = textarea.textContent
        const form = this.element.closest('form')

        let deltas = {}

        const editor = new Quill(this.element, {
            modules: {
                toolbar: toolbar,
            },
            theme: 'snow'
        })

        if (textContent) {
            deltas = JSON.parse(textContent)
        }
        editor.setContents(deltas)

        if (form) {
            form.addEventListener('formdata', (event) => {
                event.formData.append(this.element.dataset.name, JSON.stringify(editor.getContents().ops))
            })
        }
    }
};
