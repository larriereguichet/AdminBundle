import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";
import "tom-select/dist/css/tom-select.bootstrap5.css";

export default class extends Controller {
    initialize() {
        new TomSelect(this.element, {
            create: this.element.dataset.allowAdd ?? true,
            maxItems: this.element.dataset.multiple ?? null ? null : 1,
            render: {
                item: function(data, escape) {
                    console.log(data)
                    return '<span class="item badge me-1 bg-secondary" title="' + escape(data.text) + '">' + escape(data.text) + '</span>';
                }
            }
        })
    }
}
