<template>
  <div>
    <loading-spinner v-if="customerLoading"></loading-spinner>
    <p v-else-if="notFound" class="text-center">Customer not found</p>
    <div v-else>
      <div class="row mb-2">
        <div class="col-12 col-md-6">
          <h1 class="h2">Customer Details</h1>
        </div>
        <div class="col-12 col-md-6 text-md-right">
          <b-button variant="warning" v-b-modal.addEditModal> Edit </b-button>
          <b-button variant="danger" v-b-modal.deleteModal> Delete </b-button>
        </div>
      </div>
      <div v-if="customer" class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 font-20">
              <span class="label">Name: {{ customer.name }}</span>
            </div>
            <div class="col-12 col-lg-6 font-20">
              <span class="label">
                Current Balance:
                <span
                  :class="
                    customer.current_balance >= 0
                      ? 'text-success'
                      : 'text-danger'
                  "
                >
                  ৳ {{ Number(customer.current_balance).toFixed(2) }}
                </span>
              </span>
            </div>
          </div>
          <hr />
          <div class="row">
            <div class="col-12 col-lg-6">
              <span class="label">Mobile Number:</span>
              <a :href="'tel:' + customer.mobile">
                {{ customer.mobile }}
              </a>
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Email Address:</span>
              {{ customer.email || "" }}
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Address:</span>
              {{ customer.address || "" }}
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Initial Balance:</span>
              <span
                :class="
                  customer.initial_balance >= 0 ? 'text-success' : 'text-danger'
                "
              >
                ৳ {{ Number(customer.initial_balance).toFixed(2) }}
              </span>
            </div>
          </div>
          <hr />
          <div class="row">
            <div class="col-12 col-lg-6">
              <span class="label">Added by:</span>
              <router-link :to="'/user/' + customer.added_by_id">
                {{ customer.added_by_name }}
              </router-link>
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Date Addded:</span>
              {{ customer.date_added }}
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Last Updated:</span>
              {{ customer.last_updated }}
            </div>
          </div>
          <hr />
          <div class="row">
            <div class="col-12">
              <router-link
                :to="{
                  name: 'Transactions',
                  query: { customerId: customerId },
                }"
              >
                Show all transactions of {{ customer.name }}
              </router-link>
            </div>
            <!-- <div class="col-12">
              <router-link
                :to="{ name: 'Receipts', query: { customerId: customerId } }"
              >
                Show all receipts of {{ customer.name }}
              </router-link>
            </div> -->
          </div>
        </div>
      </div>

      <b-modal
        id="addEditModal"
        title="Edit Customer"
        size="lg"
        hide-footer
        no-close-on-backdrop
        scrollable
      >
        <customer-add-edit-form
          :customer="customer"
          @customer-added="onCustomerEdited"
        />
      </b-modal>

      <b-modal
        id="deleteModal"
        title="Delete Customer"
        size="sm"
        no-close-on-backdrop
        centered
        header-bg-variant="danger"
      >
        <div>
          Are you sure you want to delete customer:
          {{ customer.name }}
        </div>
        <template #modal-footer>
          <div class="w-100 text-right">
            <b-button variant="danger" size="sm" @click="deleteCustomer">
              Yes
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="hideModal('deleteModal')"
            >
              No
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import LoadingSpinner from "../../components/ui/LoadingSpinner.vue";
import CustomerAddEditForm from "../../components/CustomerAddEditForm.vue";

export default {
  components: {
    LoadingSpinner,
    CustomerAddEditForm,
  },
  data() {
    return {
      customer: null,
      notFound: null,
      customerLoading: true,
      customerId: null,
    };
  },
  methods: {
    async getCustomer() {
      try {
        const response = await axios.get("/api/customer?id=" + this.customerId);
        this.customer = response.data.customer;
      } catch (error) {
        if (error.response.status === 404) {
          this.notFound = true;
        }
      }
      this.customerLoading = false;
    },
    onCustomerEdited(response) {
      this.customer = response.data.customer;
      this.hideModal("addEditModal");
      this.makeToast(response.data.message, "success", "Success");
    },
    deleteCustomer() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteCustomer", this.customer)
        .then((res) => {
          this.showFullPageLoading = false;
          this.makeToast(res.data.message, "success", "Success");
        })
        .catch((err) => {
          this.showFullPageLoading = false;
          this.makeToast(err.response.data.message, "danger", "Cannot delete");
        });
    },
  },
  created() {
    this.customerId = this.$route.params.id;
    const data = this.$store.state.customer.customers.find(
      (m) => m.id === this.customerId
    );
    if (data) {
      this.customer = data;
      this.customerLoading = false;
    } else {
      this.getCustomer();
    }
  },
};
</script>

<style>
.label {
  font-weight: 600;
}
</style>