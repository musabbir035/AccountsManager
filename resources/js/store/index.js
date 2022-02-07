import Vuex from 'vuex';
import Vue from 'vue';
import user from './modules/user';
import customer from './modules/customer';
import transaction from './modules/transaction';
import receipt from './modules/receipt';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    user,
    customer,
    transaction,
    receipt,
  },
});
