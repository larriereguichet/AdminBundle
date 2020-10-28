import { Application } from "stimulus";
import { definitionsFromContext } from "stimulus/webpack-helpers";
import '../scss/admin.scss';

const application = Application.start();
const context = require.context("./controllers", true, /\.js$/);
application.load(definitionsFromContext(context));



// import 'jquery';
// import 'popper.js/dist/popper';
// import 'bootstrap/dist/js/bootstrap';
// import 'bootstrap/dist/css/bootstrap.css';
//
// import '@fortawesome/fontawesome-free/js/all.js';
// //import '@fortawesome/fontawesome-free/css/all.css';
//
// import '../scss/admin.scss';
// import Collapse from "./components/Collapse";
//
// // Collapse
// document.querySelectorAll('.link-toggle').forEach((element) => {
//     let collapse = new Collapse(element);
//     collapse.bind();
// });

// document.querySelectorAll('.dropdown-toggle').forEach(element => {
//   console.log(element);
//   element.addEventListener('click', event => {
//     const targetId = element.getAttribute('id');
//     console.log('id', targetId);
//
//     if (!targetId) {
//       return;
//     }
//     const targets = document.querySelectorAll('[aria-labelledby="' + targetId + '"]').forEach(container => {
//       container.classList.toggle('show');
//       console.log(container);
//     });
//   });
//
//
// });
