import {Controller} from '@hotwired/stimulus'
import Quill from 'quill'
import { HtmlToDelta } from 'quill-delta-from-html'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'

export default class extends Controller {
    connect() {
        const toolbar = JSON.parse(this.element.dataset.toolbar ?? 'true')
        const textarea = this.element.querySelector('textarea')
        let content = textarea.textContent

        const editor = new Quill(this.element, {
            modules: {
                toolbar: toolbar,
            },
            theme: 'snow'
        })

        if (content) {
            content = JSON.parse(content)
        } else {
            content = {}
        }
        editor.setContents(content)
        const form = this.element.closest('form')

        if (form) {
            form.addEventListener('formdata', (event) => {
                event.formData.append(this.element.dataset.name, JSON.stringify(editor.getContents().ops))
            })
        }
    }
};
