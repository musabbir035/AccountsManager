<template>
  <div>
    <loading-spinner v-if="isLoading"></loading-spinner>
    <p v-else-if="notFound" class="text-center">Transaction not found</p>
    <div v-else>
      <div class="row mb-2">
        <div class="col-12 col-md-6">
          <h1 class="h2">Transaction Details</h1>
        </div>
        <div class="col-12 col-md-6 text-md-right">
          <b-button variant="warning" v-b-modal.addEditModal> Edit </b-button>
          <b-button variant="danger" v-b-modal.deleteModal> Delete </b-button>
        </div>
      </div>
      <div v-if="transaction" class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6 font-20">
              <span class="label">Id:</span>
              {{ transaction.id }}
            </div>
            <div class="col-12 col-lg-6 font-20">
              <span class="label">Description:</span>
              {{ transaction.description }}
            </div>
            <div class="col-12 col-lg-6 font-20">
              <span class="label">Type:</span>
              {{ transaction.type }}
            </div>
            <div class="col-12 col-lg-6 font-20">
              <span class="label"> Amount: </span>
              <span
                :class="
                  transaction.type_id == 1 ? 'text-success' : 'text-danger'
                "
              >
                ৳ {{ Number(transaction.amount).toFixed(2) }}
              </span>
            </div>
          </div>
          <hr />
          <div class="row">
            <div class="col-12 col-lg-6">
              <span class="label">Date:</span>
              {{ transaction.date }}
            </div>
            <div class="col-12 col-lg-6" v-if="transaction.customer">
              <span class="label">Customer:</span>
              <router-link
                :to="{
                  name: 'CustomerDetails',
                  params: { id: transaction.customer.id },
                }"
              >
                {{ transaction.customer.name }}
              </router-link>
            </div>
            <hr />
            <div class="col-12 col-lg-6">
              <span class="label">Added by:</span>
              {{ transaction.added_by_name }}
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Date added:</span>
              {{ transaction.date_added }}
            </div>
            <div class="col-12 col-lg-6">
              <span class="label">Last updated:</span>
              {{ transaction.last_updated }}
            </div>
          </div>
        </div>
      </div>

      <b-modal
        id="addEditModal"
        title="Edit Transaction"
        size="lg"
        hide-footer
        no-close-on-backdrop
        scrollable
      >
        <transaction-add-edit-form
          :transaction="transaction"
          @transaction-added-edited="onTransactionEdited"
        />
      </b-modal>

      <b-modal
        id="deleteModal"
        title="Delete Transaction"
        size="sm"
        no-close-on-backdrop
        centered
        header-bg-variant="danger"
      >
        <div>Are you sure you want to delete this transaction?</div>
        <template #modal-footer>
          <div class="w-100 text-right">
            <b-button variant="danger" size="sm" @click="deleteTransaction">
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
import TransactionAddEditForm from "../../components/forms/TransactionAddEditForm.vue";

export default {
  components: {
    LoadingSpinner,
    TransactionAddEditForm,
  },
  data() {
    return {
      transaction: null,
      notFound: null,
      isLoading: true,
      transactionId: null,
    };
  },
  methods: {
    async getTransaction() {
      try {
        console.log(this.transactionId);
        const response = await axios.get(
          "/api/transactions/" + this.transactionId
        );
        this.transaction = response.data.transaction;
      } catch (error) {
        if (error.response.status === 404) {
          this.notFound = true;
        }
      }
      this.isLoading = false;
    },
    onTransactionEdited(response) {
      this.transaction = response.data.transaction;
      this.hideModal("addEditModal");
      this.makeToast(response.data.message, "success", "Success");
    },
    deleteTransaction() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteTransaction", this.transaction)
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
    this.transactionId = this.$route.params.id;
    const data = this.$store.state.transaction.transactions.find(
      (m) => m.id === this.transactionId
    );
    if (data) {
      this.transaction = data;
      this.isLoading = false;
    } else {
      this.getTransaction();
    }
  },
};
</script>

<style>
.label {
  font-weight: 600;
}
</style>