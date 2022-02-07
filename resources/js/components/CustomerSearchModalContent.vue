<template>
  <div>
    <input
      type="text"
      class="form-control"
      v-model="searchQuery"
      @input="searchCustomer"
      id="searchQuery"
      placeholder="Enter name, id or mobile number..."
    />
    <div class="search-results">
      <div
        v-for="customer in customers"
        :key="customer.id"
        class="search-item"
        @click="selectCustomer(customer)"
      >
        <span>{{ customer.name }}</span>
        <span>
          <small>{{ customer.mobile }}</small>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: null,
      selectedCustomer: null,
    };
  },
  methods: {
    searchCustomer() {
      if (this.searchQuery.length >= 3) {
        this.$store
          .dispatch("getAllCustomers", this.searchQuery)
          .then((res) => {
            console.log(res.data);
          })
          .catch((err) => {
            console.log(err.response);
          });
      }
    },
    selectCustomer(customer) {
      this.$emit("customerSelected", customer);
    },
  },
  computed: {
    customers() {
      return this.$store.state.customer.customers;
    },
  },
};
</script>

<style>
</style>