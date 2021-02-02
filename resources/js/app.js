/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// BOOTSTRAP VUE
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(require('vue-moment'));

import vueNumeralFilterInstaller from 'vue-numeral-filter';
 
Vue.use(vueNumeralFilterInstaller, { locale: 'en-gb' });


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// COMPONENTES DE VAUCHERS
Vue.component('vouchers-component', require('./components/vouchers/VouchersComponent.vue').default);

// COMPONENTES PARA EL REGISTRO
Vue.component('register-component', require('./components/register/RegisterComponent.vue').default);
Vue.component('registers-component', require('./components/register/RegistersComponent.vue').default);
Vue.component('pre-register-component', require('./components/register/PreRegisterComponent.vue').default);
Vue.component('search-folio', require('./components/register/SearchFolio.vue').default);
Vue.component('delivery-component', require('./components/register/DeliveryComponent.vue').default);


Vue.component('consult-register-component', require('./components/register/ConsultRegisterComponent.vue').default);

// COMPONENTES PARA LOS CODIGOS
Vue.component('codes-component', require('./components/codes/CodesComponent.vue').default);

// COMPONENTES PARA SUBIR FOLIOS A LA BASE DE DATOS
Vue.component('files-component', require('./components/manager/FilesComponent.vue').default);


Vue.component('folios-component', require('./components/FoliosComponent.vue').default);


// SCHOOL
Vue.component('schools-component', require('./components/schools/SchoolsComponent.vue').default);
Vue.component('new-edit-school', require('./components/schools/NewEditSchool.vue').default);

// BOOKS
Vue.component('books-component', require('./components/books/BooksComponent.vue').default);
Vue.component('new-edit-book', require('./components/books/NewEditBook.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
