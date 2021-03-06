import { Controller } from 'stimulus';
import $ from 'jquery';
import select2 from 'select2/dist/js/select2';
import client from 'axios';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-theme/dist/select2-bootstrap.css';

export default class extends Controller {
    initialize() {
        let options = JSON.parse(this.element.dataset.options) || {};
        const endpoint = this.element.dataset.endpoint;
        const allowAdd = this.element.dataset.allowAdd;
        //options = (options.length === 0) ? {} : options;

        if (allowAdd) {
            options.createTag = params => {
                let term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            };
            // options.insertTag = function (data, tag) {
            //     //console.log(data, tag, tag.value);
            //     // Insert the tag at the end of the results
            //     data.unshift(tag);
            // };
            // options.formatSate = function (state) {
            //     console.log(state);
            // };
        }

        $(this.element).select2(options);

        // if (allowAdd) {
        //     $(this.element).on('select2:select', event => {
        //         event.params.data.id = 666;
        //         //console.log(event);
        //     });
        //
        // }
    }
}
