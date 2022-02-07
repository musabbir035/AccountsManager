<template>
  <div>
    <navigation
      @open-ledger-modal="openLedgerModal"
      @change-margin="updateMargin"
    />

    <div class="main" :class="mainMargin">
      <router-view @open-ledger-modal="openLedgerModal"></router-view>
    </div>

    <b-modal
      id="ledgerModal"
      title="Generate Ledger"
      size="md"
      no-close-on-backdrop
      centered
      header-bg-variant="primary"
      hide-footer
    >
      <ledger-form
        @open-search="openSearchModal"
        :selectedCustomer="selectedCustomer"
      />
    </b-modal>

    <b-modal
      id="searchCustomerModal"
      title="Search Customer"
      size="md"
      hide-footer
      no-close-on-backdrop
      @hidden="searchQuery = ''"
      @shown="focusSearchInput"
    >
      <customer-search-modal-content @customerSelected="customerSelected" />
    </b-modal>
  </div>
</template>

<script>
import Navigation from "./Navigation.vue";
import LedgerForm from "../LedgerForm.vue";

export default {
  components: {
    Navigation,
    LedgerForm,
  },
  data() {
    return {
      mainMargin: null,
      searchQuery: null,
      selectedCustomer: null,
    };
  },
  methods: {
    updateMargin(e) {
      if (e) {
        this.mainMargin = "left-margin";
      } else {
        this.mainMargin = "no-margin";
      }
    },
    openLedgerModal(id) {
      this.$bvModal.show("ledgerModal");
    },
    openSearchModal() {
      this.$bvModal.show("searchCustomerModal");
    },
    focusSearchInput() {
      document.querySelector("#searchQuery").focus();
    },
    customerSelected(customer) {
      this.hideModal("searchCustomerModal");
      this.selectedCustomer = customer;
    },
  },
};
</script>
