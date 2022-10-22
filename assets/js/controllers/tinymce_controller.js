// import {Controller} from "@hotwired/stimulus";
//
// /* Import TinyMCE */
// import tinymce from 'tinymce';
//
// /* Default icons are required. After that, import custom icons if applicable */
// import 'tinymce/icons/default';
//
// /* Required TinyMCE components */
// import 'tinymce/themes/silver';
// import 'tinymce/models/dom';
//
// /* Import a skin (can be a custom skin instead of the default) */
// import 'tinymce/skins/ui/oxide/skin.css';
//
// /* Import plugins */
// import 'tinymce/plugins/advlist';
// import 'tinymce/plugins/code';
// import 'tinymce/plugins/emoticons';
// import 'tinymce/plugins/emoticons/js/emojis';
// import 'tinymce/plugins/link';
// import 'tinymce/plugins/lists';
// import 'tinymce/plugins/table';
//
// /* content UI CSS is required */
// import contentUiSkinCss from 'tinymce/skins/ui/oxide/content.css';
//
// /* The default content CSS can be changed or replaced with appropriate CSS for the editor content. */
// import contentCss from 'tinymce/skins/content/default/content.css';
//
// /* Initialize TinyMCE */
// export function render () {
//     tinymce.init({
//         selector: 'textarea#editor',
//         plugins: 'advlist code emoticons link lists table',
//         toolbar: 'bold italic | bullist numlist | link emoticons',
//         skin: false,
//         content_css: false,
//         content_style: contentUiSkinCss.toString() + '\n' + contentCss.toString(),
//     });
// };
//
// export default class extends Controller {
//     initialize() {
//         const dataset = this.element.dataset;
//         this.options = JSON.parse(dataset.options) || {};
//         const customButtons = JSON.parse(dataset.customButtons) || {};
//
//         this.options.setup = function (editor) {
//             for (const key in customButtons) {
//                 if (customButtons.hasOwnProperty(key)) {
//                     let event = new CustomEvent(customButtons[key].event);
//
//                     editor.ui.registry.addButton(key, {
//                         text: customButtons[key].text,
//                         onAction: function () {
//                             window.dispatchEvent(event);
//                         }
//                     });
//                 }
//             }
//         };
//
//         window.addEventListener('tinymce-insert-content', event => {
//             this.insert(event.detail);
//         });
//     }
//
//     connect() {
//         let event = new CustomEvent('tinymce-initialize', {
//             detail: this.options
//         });
//         window.dispatchEvent(event);
//         tinymce.init(event.detail);
//     }
//
//     insert(content) {
//         tinymce.activeEditor.insertContent(content);
//     }
// }
