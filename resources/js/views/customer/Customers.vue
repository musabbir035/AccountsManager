<template>
  <div>
    <div class="row mb-2">
      <div class="col-12 col-md-6">
        <h1 class="h2">Customers</h1>
      </div>
      <div class="col-12 col-md-6 text-md-right">
        <b-button variant="primary" v-b-modal.customerAddEditModal>
          Add Customer
        </b-button>
      </div>
    </div>

    <b-card class="mb-2 table-responsive">
      <loading-spinner v-if="loadingStatus"></loading-spinner>
      <p v-else-if="errorMessage" class="text-danger text-center">
        {{ errorMessage }}
      </p>
      <div v-else>
        <b-row class="mb-3">
          <b-col sm="12" md="5" lg="3">
            <b-input-group size="sm">
              <b-form-input
                id="filter-input"
                v-model="filter"
                type="search"
                placeholder="Type to search customers..."
              ></b-form-input>
            </b-input-group>
          </b-col>
        </b-row>

        <b-table
          striped
          :items="customers"
          :fields="tableFields"
          :filter="filter"
          :current-page="currentPage"
          :per-page="perPage"
          stacked="sm"
        >
          <template #cell(name)="row">
            <router-link
              :title="row.item.name"
              :to="{ name: 'CustomerDetails', params: { id: row.item.id } }"
            >
              {{
                row.item.name.substring(0, 30) +
                (row.item.name.length > 30 ? "..." : "")
              }}
            </router-link>
          </template>

          <template #cell(current_balance)="row">
            <span
              :class="
                row.item.current_balance < 0 ? 'text-danger' : 'text-success'
              "
            >
              ৳ {{ Number(row.item.current_balance).toFixed(2) }}
            </span>
          </template>

          <template #cell(actions)="row">
            <b-button
              size="xs"
              class="mr-1 mt-1"
              variant="info"
              :to="{ name: 'CustomerDetails', params: { id: row.item.id } }"
            >
              Details
            </b-button>
            <b-button
              size="xs"
              @click="
                openCustomerAddEditModal(row.item, row.index, $event.target)
              "
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

    <b-row class="mt-4" v-if="$store.state.customer.customersCount > perPage">
      <b-col sm="7" md="6" lg="4" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="customersCount"
          :per-page="perPage"
          align="fill"
          size="sm"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>

    <b-modal
      id="customerAddEditModal"
      :title="customerAddEditModalTitle"
      size="lg"
      hide-footer
      no-close-on-backdrop
      scrollable
      @hidden="resetModal"
    >
      <customer-add-edit-form
        :customer="beingEditedCustomer"
        @customer-added="onCustomerAdded"
      />
    </b-modal>

    <b-modal
      id="deleteModal"
      title="Delete Customer"
      size="sm"
      no-close-on-backdrop
      centered
      header-bg-variant="danger"
      @hidden="resetModal"
    >
      <div>
        Are you sure you want to delete customer:
        {{ beingEditedCustomer ? beingEditedCustomer.name : "" }}
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

    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import LoadingSpinner from "../../components/ui/LoadingSpinner.vue";
import CustomerAddEditForm from "../../components/CustomerAddEditForm.vue";

export default {
  components: {
    LoadingSpinner,
    CustomerAddEditForm,
  },
  data() {
    return {
      tableFields: [
        {
          key: "id",
          sortable: true,
        },
        {
          key: "name",
          sortable: true,
        },
        {
          key: "mobile",
          label: "Mobile Number",
          sortable: true,
        },
        {
          key: "current_balance",
          sortable: true,
        },
        {
          key: "actions",
        },
      ],
      showFullPageLoading: false,
      filter: null,
      currentPage: 1,
      perPage: 20,
      beingEditedCustomer: null,
      loadingStatus: true,
      errorMessage: null,
      customerAddEditModalTitle: "Add Customer",
    };
  },
  methods: {
    resetModal() {
      this.beingEditedCustomer = null;
      this.customerAddEditModalTitle = "Add Customer";
    },
    openCustomerAddEditModal(item, index, event) {
      this.beingEditedCustomer = item;
      this.customerAddEditModalTitle = "Edit Customer";
      this.$bvModal.show("customerAddEditModal");
    },
    openDeleteModal(item, index, event) {
      this.beingEditedCustomer = item;
      this.$bvModal.show("deleteModal");
    },
    onCustomerAdded(response) {
      this.hideModal("customerAddEditModal");
      this.makeToast(response.data.message, "success", "Success");
    },
    deleteCustomer() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteCustomer", this.beingEditedCustomer)
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
  computed: {
    customers() {
      return this.$store.state.customer.customers;
    },
    customersCount() {
      return this.$store.state.customer.customersCount;
    },
  },
  created() {
    this.$store
      .dispatch("getAllCustomers")
      .then((res) => {
        this.loadingStatus = false;
      })
      .catch((err) => {
        this.loadingStatus = false;
        if (this.customersCount <= 0) {
          this.errorMessage = "Something went wrong";
        }
      });
  },
};
</script>