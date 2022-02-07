import Vue from 'vue';
import { BootstrapVue } from 'bootstrap-vue';

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
  },
});
