require("./bootstrap");

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import { IconsPlugin } from "bootstrap-vue";
import mixin from "./util/mixin";
import CustomerSearchModalContent from "./components/CustomerSearchModalContent.vue";
import LoadingSpinner from "./components/ui/LoadingSpinner.vue";

require("./vee-validate-setup");

import "../sass/app.scss";

Vue.config.productionTip = false;

Vue.use(IconsPlugin);
Vue.component("customer-search-modal-content", CustomerSearchModalContent);
Vue.component("loading-spinner", LoadingSpinner);

Vue.prototype.$appName = "Accounts Manager";

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount("#app");
