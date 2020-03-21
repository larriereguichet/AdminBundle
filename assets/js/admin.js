import 'jquery';
import 'popper.js/dist/popper';
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/dist/css/bootstrap.css';

import '@fortawesome/fontawesome-free/js/all.js';
import '@fortawesome/fontawesome-free/css/all.css';

import '../scss/admin.scss';
import Collapse from "./components/Collapse";

// Collapse
document.querySelectorAll('.link-toggle').forEach((element) => {
    let collapse = new Collapse(element);
    collapse.bind();
});
