import Vue from "vue";
import { BootstrapVue } from "bootstrap-vue";

Vue.use(BootstrapVue);
Vue.mixin({
  methods: {
    makeToast(msg, variant = null, title = null) {
      this.$bvToast.toast(msg, {
        autoHideDelay: 5000,
        variant: variant,
        title: title,
      });
    },

    hideModal(id) {
      this.$bvModal.hide(id);
    },

    dateFormat(dt) {
      let date = new Date(dt);
      var d = date.getDate();
      var m = date.getMonth() + 1;
      var y = date.getFullYear();
      return y + "-" + (m <= 9 ? "0" + m : m) + "-" + (d <= 9 ? "0" + d : d);
    },
  },
});
