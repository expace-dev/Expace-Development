/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

import './navbar-mobile'

// start the Stimulus application
import './bootstrap';

import bootstrap from "bootstrap";
window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');

import '@grafikart/drop-files-element';

import {Tooltip} from 'bootstrap';

const tooltipTriggerList = [...document.querySelectorAll('[data-bs-toggle="tooltip"]')];
const tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new Tooltip(tooltipTriggerEl));