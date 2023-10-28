import {Controller} from "@hotwired/stimulus";
import * as bootstrap from 'bootstrap'

export default class extends Controller {
    connect() {
        this.element.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', event => {
                const tab = new bootstrap.Tab(link)
                tab.show()
                event.preventDefault()
            })
        })
    }
};
