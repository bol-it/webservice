require('./bootstrap');
import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import ru from 'vuetify/src/locale/ru.ts';

import VueRouter from 'vue-router';
import VueClipboard from 'vue-clipboard2';
import { Plugin } from 'vue2-storage';
import VueWorker from 'vue-worker';
import VueSSE from 'vue-sse';

var button_up = require('./components/Button_Up.Component.vue').default;
var top_forms = require('./components/top_forms.vue').default;
var footer_forms = require('./components/footer_forms.vue').default;
var forms = require('./components/forms.vue').default;
var new_edit_form = require('./components/new_edit_form.vue').default;
var form_reports = require('./components/form_reports.vue').default;
var fill_form = require('./components/fill_form.vue').default;
var view_result_form = require('./components/view_result_form.vue').default;

Vue.component('VInputNumber', require('./components/form_text_number.vue').default);
Vue.component('VInputDate', require('./components/form_date_field.vue').default);

Vue.use(Vuetify);
Vue.use(VueRouter);
Vue.use(VueClipboard);
Vue.use(Plugin);
Vue.use(VueWorker);
Vue.use(VueSSE);

const routes = [
    {
        path: '/forms', component: forms,
        name: 'forms',
        props: true
    },
    {
        path: '/forms/new_forms', component: new_edit_form,
        props: true
    },
    {
        path: '/forms/edit_forms', component: new_edit_form,
        props: (route) => ({ id: route.query.id, })
    },
    {
        path: '/forms/reports', component: form_reports,
        props: true
    },
    {
        path: '/forms/fill_form', component: fill_form,
        name: 'fill_form',
        props: (route) => ({ id: route.query.id, })
    },
    {
        path: '/forms/view_result_form', component: view_result_form,
        name: 'view_result_form',
        props: (route) => ({ id: route.query.id, })
    },
];

const router = new VueRouter({
  mode: 'history',
  base: '/webservice/public/',
  routes
});

var inputHandler = null;
//v-filter= "{ decimal: '.', thousands: ' ',}""
Vue.directive("filter", {
  bind: function(el, binding, VNode) {
    let delimiter = ' ';
    let decimal = '.';
    let allowable_regexp;
    switch (binding.value.decimal) {
      case '.':
        allowable_regexp = '[0-9.]';
        decimal = '.';
      break;
      case ',':
        allowable_regexp = '[0-9,]';
        decimal = ',';
      break;
      default:
        allowable_regexp = '[0-9.]';
        decimal = '.';
      break;
    }
    switch (binding.value.thousands) {
      case ' ':
      case '':
        delimiter = binding.value.thousands;
      break;
      default:
        delimiter = ' ';
      break;
    }
    let regexp = /[\d]/gi;
    inputHandler = function(e) {
      //var ch = String.fromCharCode(e.which);
      var ch = e.key;
      var re = new RegExp(allowable_regexp);
      if ((e.ctrlKey || e.metaKey) && e.keyCode == 86) {
        console.log('ctrl + V');
        console.log('Вставка пока не реализована');
      }
      if (e.keyCode == 8) {
        if ( VNode.context.value_text.length > 0) {
          let float = VNode.context.value_text.split(decimal);
          if (float.length == 2) {
            let str_float =  float[0];
            str_float = str_float.match(regexp).join('');
            str_float = str_float.replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
            str_float = str_float + decimal + float[1];
            str_float = str_float.slice(0,-1);
            VNode.context.value_text = str_float;
          }
          else {
            let str_float =  float[0] + ch;
            str_float = str_float.match(regexp).join('');
            str_float = str_float.slice(0,-1);
            str_float = str_float.replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
            VNode.context.value_text = str_float;
          }
        }
      }
      e.preventDefault();
      if (!ch.match(re)) {
        e.preventDefault();
      }
      else {
        if (!VNode.context.value_text) {
          VNode.context.value_text = '';
        }
        if (VNode.context.value_text.length < 55) {
          let float = VNode.context.value_text.split(decimal);
          if (float.length == 2) {
            if (ch != decimal) {
              if (float[1].length < 2) {
                let str_float =  float[0];
                str_float = str_float.match(regexp).join('');
                str_float = str_float.replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
                str_float = str_float + decimal + float[1] + ch;
                VNode.context.value_text = str_float;
              }
            }
          }
          else {
            let str_float =  float[0] + ch;
            if ((str_float.length == 1) && (ch == decimal)) str_float = '0';
            str_float = str_float.match(regexp).join('');
            str_float = str_float.replace(/\B(?=(\d{3})+(?!\d))/g, delimiter);
            if (ch == decimal) str_float += ch;
            VNode.context.value_text = str_float;
          }
        }
      }
    };
    el.addEventListener("keydown", inputHandler);
  },
  unbind: function(el) {
    el.removeEventListener("keydown", inputHandler);
  },
  inputHandler: null
});

new Vue({
    data: {
        current_url: '',
    },
    components: {
        button_up, top_forms, footer_forms,
    },
    router,
    vuetify: new Vuetify({
        icons: {
            iconfont: 'mdiSvg', // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
        },
        lang: {
            locales: { ru },
            current: 'ru',
        },
    })
}).$mount('#app');