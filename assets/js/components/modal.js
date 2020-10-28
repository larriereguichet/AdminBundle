import $ from 'jquery';
import client from 'axios';
import 'bootstrap/dist/js/bootstrap';

export default class Modal {
    constructor(selector, options, contentUrl) {
        options = options || {};
        options.show = false;

        this.element = document.querySelector(selector);
        this.modal = $(selector).modal(options);
        this.loader = document.querySelector('#admin-loader');

        if (contentUrl) {
            const modal = this.modal;
            this.element.querySelector('div.modal-body').innerHTML = this.loader.outerHTML;
            this
                .modal
                .off('show.bs.modal')
                .on('show.bs.modal', function () {
                    client
                        .get(contentUrl)
                        .then(response => {
                            modal.html(response.data);
                        })
                        .catch(error => alert(error))
                    ;
                })
            ;
        }
        this.modal.modal('show');
    }
}
