import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        console.log('gggggggggggggggggggggggggg')

    }

    hideImage() {
        console.log('click ?')
        this.element.querySelector('.image-card').classList.add('d-none')
    }
};
