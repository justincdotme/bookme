window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

window.Vue = require('vue');

window.axios = require('axios');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.requestTokens.csrf;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';