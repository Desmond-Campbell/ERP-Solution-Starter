import Vue from 'vue';
import VueQueryBuilder from 'vue-query-builder';
import axios from 'axios';

import BootstrapVue from './node_modules/bootstrap-vue/dist/bootstrap-vue.js'
Vue.use( BootstrapVue );

import VueUploadComponent from 'vue-upload-component';
Vue.component('file-upload', VueUploadComponent);

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import VueColor from 'vue-color';

import DatePicker from 'vue2-datepicker';
Vue.component('date-picker', DatePicker);

import VueProgressBar from 'vue-progressbar';

Vue.use(VueProgressBar, {
  color: 'rgb(224, 146, 8)',
  failedColor: 'rgb(224, 18, 8)',
  height: '3px'
});

import CripNotice from 'crip-vue-notice';
Vue.use(CripNotice);

import VuejsDialog from "vuejs-dialog";
Vue.use(VuejsDialog);

// @import './node_modules/vue-on-toast/dist/vue-on-toast.css';

export const eventBus = new Vue();

if ( typeof Modules !== 'undefined' ) {

    for ( var m = 0; m < Modules.length; m++ ) {

      require( './Controllers/' + Modules[m] + '.js' );

    }

}

/////////////////////////

window.app = new Vue({
    el: "#app",
    data: {},
    components: {
      // 'toast-container' : VueOnToast
    },
    methods: {
      alert (type, title, message, duration) {
        this.$notice[type]({
          title: `${title}`,
          description: `${message}`,
          duration: duration
        })
      },
    },
    computed: {}
});

////////////////////////

window.header = new Vue({
    el: "#header",
    data: {},
    computed: {}
});
