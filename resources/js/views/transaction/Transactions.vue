<template>
  <div>
    <div class="row mb-2">
      <div class="col-12 col-md-6">
        <h1 class="h2">Transactions</h1>
      </div>
      <div class="col-12 col-md-6 text-md-right">
        <b-button variant="primary" v-b-modal.addEditModal>
          Add New Transaction
        </b-button>
      </div>
    </div>

    <b-card class="mb-2 table-responsive">
      <loading-spinner v-if="loadingStatus"></loading-spinner>
      <p v-else-if="errorMessage" class="text-danger text-center">
        {{ errorMessage }}
      </p>
      <div v-else>
        <div v-if="customer" class="mb-3">
          <div>
            Showing transactions of
            <router-link
              :to="{ name: 'CustomerDetails', params: { id: customerId } }"
            >
              {{ customer.name }}.
            </router-link>
          </div>
          <div>
            <router-link :to="{ name: 'Transactions' }">
              Show all transactions
            </router-link>
          </div>
        </div>
        <b-row class="mb-3">
          <b-col sm="12" md="6" lg="3">
            <label for="filter-input">Search</label>
            <b-form-input
              id="filter-input"
              v-model="filterText"
              type="search"
              placeholder="Type to search transactions..."
              class="form-control-sm"
            ></b-form-input>
          </b-col>
          <b-col sm="12" md="6" lg="3">
            <label for="date">Filter Date</label>
            <date-picker
              v-model="filterDateRange"
              type="date"
              range
              placeholder="Select date"
              id="date"
            ></date-picker>
          </b-col>
        </b-row>

        <b-table
          striped
          :items="transactions"
          :fields="tableFields"
          :filter="filter"
          :filter-function="filterTable"
          :current-page="currentPage"
          :per-page="perPage"
          stacked="sm"
          show-empty
        >
          <template #empty> No transactions to show </template>
          <template #cell(amount)="row">
            <span :class="row.item.amount < 0 ? 'text-danger' : 'text-success'">
              ৳ {{ Number(row.item.amount).toFixed(2) }}
            </span>
          </template>
          <template #cell(actions)="row">
            <b-button
              size="xs"
              class="mr-1 mt-1"
              variant="info"
              :to="{ name: 'TransactionDetails', params: { id: row.item.id } }"
            >
              Details
            </b-button>
            <b-button
              size="xs"
              @click="openEditModal(row.item, row.index, $event.target)"
              class="mr-1 mt-1"
              variant="warning"
            >
              Edit
            </b-button>
            <b-button
              v-if="$store.state.user.authUser.role_id == 1"
              size="xs"
              @click="openDeleteModal(row.item, row.index, $event.target)"
              class="mt-1"
              variant="danger"
            >
              Delete
            </b-button>
          </template>
        </b-table>
      </div>
    </b-card>

    <b-row
      class="mt-4"
      v-if="$store.state.transaction.transactionsCount > perPage"
    >
      <b-col sm="12"> Total {{ transactionsCount }} transactions </b-col>
      <b-col sm="7" md="6" lg="4" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="transactionsCount"
          :per-page="perPage"
          align="fill"
          size="sm"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>

    <b-modal
      id="addEditModal"
      :title="addEditTitle"
      size="lg"
      hide-footer
      no-close-on-backdrop
      scrollable
      header-bg-variant="primary"
      @hidden="resetModal"
    >
      <transaction-add-edit-form
        :selectedCustomer="selectedCustomer"
        :transaction="beingEditedTransaction"
        @transaction-added-edited="onAddEditTransaction"
        @open-search="openSearchModal"
      />
    </b-modal>

    <b-modal
      id="customerSearchModal"
      title="Search Customer"
      size="md"
      hide-footer
      no-close-on-backdrop
      header-bg-variant="primary"
      @hidden="searchQuery = ''"
      @shown="focusSearchInput"
    >
      <customer-search-modal-content @customerSelected="customerSelected" />
    </b-modal>

    <b-modal
      id="detailsModal"
      title="Transaction Details"
      size="md"
      hide-footer
      no-close-on-backdrop
    >
    </b-modal>

    <b-modal
      id="deleteModal"
      title="Delete Transaction"
      size="sm"
      no-close-on-backdrop
      centered
      header-bg-variant="danger"
      @hidden="resetModal"
    >
      <div>
        Are you sure you want to delete transaction:
        {{ beingEditedTransaction ? beingEditedTransaction.id : "" }}
      </div>
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

    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import TransactionAddEditForm from "../../components/forms/TransactionAddEditForm.vue";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";

export default {
  components: { TransactionAddEditForm, DatePicker },
  data() {
    return {
      tableFields: [
        {
          key: "id",
          sortable: true,
        },
        {
          key: "description",
          sortable: true,
        },
        {
          key: "type",
          sortable: true,
        },
        {
          key: "amount",
          sortable: true,
        },
        {
          key: "date",
          sortable: true,
        },
        {
          key: "actions",
        },
      ],
      showFullPageLoading: false,
      filterText: null,
      filterDateRange: null,
      currentPage: 1,
      perPage: 20,
      beingEditedTransaction: null,
      loadingStatus: null,
      errorMessage: null,
      searchQuery: null,
      selectedCustomer: null,
      selectedCustomerId: null,
      customer: null,
      customerId: null,
      notFound: null,
      addEditTitle: "Add Transaction",
    };
  },
  methods: {
    resetModal() {
      this.beingEditedTransaction = null;
      this.addEditTitle = "Add Transaction";
    },
    openEditModal(item, index, event) {
      this.beingEditedTransaction = item;
      this.addEditTitle = "Edit Transaction";
      this.$bvModal.show("addEditModal");
    },
    openDeleteModal(item, index, event) {
      this.beingEditedTransaction = item;
      this.$bvModal.show("deleteModal");
    },
    onAddEditTransaction(response) {
      this.hideModal("addEditModal");
      this.makeToast(response.data.message, "success", "Success");
    },
    deleteTransaction() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteTransaction", this.beingEditedTransaction)
        .then((res) => {
          this.showFullPageLoading = false;
          this.makeToast(res.data.message, "success");
        })
        .catch((err) => {
          this.showFullPageLoading = false;
          this.makeToast(err.response.data.message, "danger");
        });
    },
    openSearchModal() {
      this.$bvModal.show("customerSearchModal");
    },
    focusSearchInput() {
      document.querySelector("#searchQuery").focus();
    },
    customerSelected(customer) {
      this.hideModal("customerSearchModal");
      this.selectedCustomer = customer;
    },
    async getCustomer(id) {
      try {
        const response = await axios.get("/api/customer?id=" + id);
        this.customer = response.data.customer;
        if (this.customer) {
          this.getTransactions();
        }
      } catch (error) {
        if (error.response.status === 404) {
          this.notFound = true;
        }
      }
    },
    init() {
      this.loadingStatus = true;
      this.customerId = null;
      this.customer = null;
      //if route has a coustomerId, get transactions of only that customer
      //else get all transactions
      if (
        this.$route.query.customerId &&
        !Number.isNaN(this.$route.query.customerId)
      ) {
        this.customerId = this.$route.query.customerId;
        this.getCustomer(this.customerId);
      } else {
        this.getTransactions();
      }
    },
    getTransactions() {
      this.$store
        .dispatch("getTransactions", this.customerId)
        .then((res) => {
          this.loadingStatus = false;
        })
        .catch((err) => {
          this.loadingStatus = false;
          this.errorMessage = "Something went wrong";
        });
    },
    filterTable(row, filter) {
      //Filters the table.
      //filter array has two values: a search query on 1st index and
      //two dates as an array on 2nd index.
      //TODO: improve this section
      let date = new Date(row.date);
      if (filter[0] && filter[1] && filter[1][0] && filter[1][1]) {
        let val = filter[0].toLowerCase();
        let dateFrom = new Date(filter[1][0]);
        let dateTo = new Date(filter[1][1]);

        return (
          (row.description.toLowerCase().includes(val) ||
            row.type.toLowerCase().includes(val) ||
            row.amount.toLowerCase().includes(val)) &&
          date >= dateFrom &&
          date <= dateTo
        );
      } else if (filter[0]) {
        let val = filter[0].toLowerCase();
        return (
          row.description.toLowerCase().includes(val) ||
          row.type.toLowerCase().includes(val) ||
          row.amount.toLowerCase().includes(val)
        );
      } else if (filter[1] && filter[1][0] && filter[1][1]) {
        let dateFrom = new Date(filter[1][0]);
        let dateTo = new Date(filter[1][1]);
        return date >= dateFrom && date <= dateTo;
      }
    },
  },
  computed: {
    transactions() {
      return this.$store.state.transaction.transactions;
    },
    transactionsCount() {
      return this.$store.state.transaction.transactionsCount;
    },
    filter() {
      //Checks if table data should be filtered.
      //If either filterText or filterDateRange array has value
      //return both as an array else return null.
      if (
        this.filterText ||
        (this.filterDateRange &&
          this.filterDateRange[0] &&
          this.filterDateRange[1])
      ) {
        return [this.filterText, this.filterDateRange];
      }
      return null;
    },
  },
  watch: {
    $route(to, from) {
      this.init();
    },
  },
  created() {
    this.init();
  },
};
</script>