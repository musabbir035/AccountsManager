import Vue from 'vue';
import {
  ValidationProvider,
  ValidationObserver,
  extend,
  localize,
} from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';
import en from 'vee-validate/dist/locale/en.json';

Object.keys(rules).forEach((rule) => {
  extend(rule, rules[rule]);
});
localize('en', en);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

localize({
  en: {
    messages: {
      // generic rule messages...
    },
    fields: {
      password: {
        required: 'Password cannot be empty!',
        max: 'Are you really going to remember that?',
        min: 'Password must contain at least {length} characters',
      },
    },
  },
});
