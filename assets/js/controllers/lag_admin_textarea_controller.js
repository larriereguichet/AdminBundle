import {Controller} from '@hotwired/stimulus'
import 'trix'

import 'trix/dist/trix.css'

export default class extends Controller {
    connect() {
        const toolbar = JSON.parse(this.element.dataset.toolbar ?? 'true')
        const disabled = this.element.dataset.disabled === '1'

        // The custom element upgrades while the document is parsed, so trix-initialize may already have been
        // dispatched by the time Stimulus connects the controller.
        const editor = this.element.querySelector('trix-editor')

        if (editor && editor.toolbarElement) {
            this.setUp(editor, toolbar, disabled)
        }

        this.element.addEventListener('trix-initialize', (event) => {
            this.setUp(event.target, toolbar, disabled)
        })

        // Removing the button is not enough: files can also be dropped or pasted into the editor. Without an
        // upload endpoint, Trix would inline them as data URIs.
        this.element.addEventListener('trix-file-accept', (event) => {
            if (disabled || !this.isEnabled(toolbar, 'attachFiles')) {
                event.preventDefault()
            }
        })
    }

    // Trix has no disabled attribute of its own: it makes the element editable on initialize, so the read only
    // state has to be applied back afterwards.
    setUp(editor, toolbar, disabled) {
        this.applyToolbar(editor.toolbarElement, disabled ? false : toolbar)

        if (disabled) {
            editor.setAttribute('contenteditable', 'false')
        }
    }

    isEnabled(toolbar, button) {
        if (typeof toolbar === 'boolean') {
            return toolbar
        }

        return toolbar.includes(button)
    }

    applyToolbar(toolbarElement, toolbar) {
        if (toolbar === true) {
            return
        }

        if (toolbar === false) {
            toolbarElement.hidden = true

            return
        }

        toolbarElement.querySelectorAll('[data-trix-attribute], [data-trix-action]').forEach((button) => {
            if (!toolbar.includes(button.dataset.trixAttribute ?? button.dataset.trixAction)) {
                button.remove()
            }
        })

        toolbarElement.querySelectorAll('.trix-button-group').forEach((group) => {
            if (group.childElementCount === 0) {
                group.remove()
            }
        })
    }
};
