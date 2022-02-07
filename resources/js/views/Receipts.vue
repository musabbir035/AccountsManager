<template>
  <div>
    <div class="row mb-2">
      <div class="col-6">
        <h1 class="page-header">Receipts</h1>
      </div>
      <div class="col-6 text-right">
        <b-button variant="primary" v-b-modal.addEditModal>
          Add New Receipt
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
                placeholder="Type to search receipts..."
              ></b-form-input>
            </b-input-group>
          </b-col>
        </b-row>

        <b-table
          striped
          :items="receipts"
          :fields="tableFields"
          :filter="filter"
          :current-page="currentPage"
          :per-page="perPage"
          stacked="sm"
          show-empty
        >
          <template #empty> No receipts to show </template>
          <template #cell(actions)="row">
            <b-button
              size="xs"
              @click="details(row.item, row.index, $event.target)"
              class="mr-1 mt-1"
              variant="info"
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

    <b-row class="mt-4" v-if="$store.state.receipt.receiptsCount > perPage">
      <b-col sm="7" md="6" lg="4" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="receiptsCount"
          :per-page="perPage"
          align="fill"
          size="sm"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>

    <b-modal
      id="addEditModal"
      title="Add Receipt"
      size="lg"
      hide-footer
      no-close-on-backdrop
      scrollable
      @hidden="resetModal"
      header-bg-variant="primary"
    >
      <receipt-add-edit-form
        :receipt="beingEditedReceipt"
        @add-receipt="onAddReceipt"
      />
    </b-modal>

    <b-modal
      id="detailsModal"
      title="Receipt Details"
      size="md"
      hide-footer
      no-close-on-backdrop
    >
      <item-details :detailsData="receiptData" />
    </b-modal>

    <b-modal
      id="deleteModal"
      title="Delete Receipt"
      size="sm"
      no-close-on-backdrop
      centered
      header-bg-variant="danger"
      @hidden="resetModal"
    >
      <div>
        Are you sure you want to delete receipt:
        {{ beingEditedReceipt ? beingEditedReceipt.id : "" }}
      </div>
      <template #modal-footer>
        <div class="w-100 text-right">
          <b-button variant="danger" size="sm" @click="deleteReceipt">
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
import LoadingSpinner from "../components/ui/LoadingSpinner.vue";
import ReceiptAddEditForm from "../components/ReceiptAddEditForm.vue";
import ItemDetails from "../components/ItemDetails.vue";

export default {
  components: {
    LoadingSpinner,
    ReceiptAddEditForm,
    ItemDetails,
  },
  data() {
    return {
      tableFields: [
        {
          key: "id",
          label: "#",
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
          key: "total",
          sortable: true,
        },
        {
          key: "due_amount",
          sortable: true,
        },
        {
          key: "date_added",
          label: "date",
          sortable: true,
        },
        {
          key: "actions",
        },
      ],
      showFullPageLoading: false,
      filter: null,
      currentPage: 1,
      perPage: 10,
      receiptData: null,
      beingEditedReceipt: null,
      loadingStatus: null,
      errorMessage: null,
    };
  },
  methods: {
    resetModal() {
      this.beingEditedReceipt = null;
    },
    details(item, index, event) {
      this.receiptData = item;
      this.$bvModal.show("detailsModal");
    },
    openEditModal(item, index, event) {
      this.beingEditedReceipt = item;
      this.$bvModal.show("addEditModal");
    },
    openDeleteModal(item, index, event) {
      this.beingEditedReceipt = item;
      this.$bvModal.show("deleteModal");
    },
    onAddReceipt(e) {
      this.showFullPageLoading = e.loading;
      if (e.success) {
        this.hideModal("addEditModal");
        this.makeToast(e.message, "success");
      }
    },
    deleteReceipt() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteReceipt", this.beingEditedReceipt)
        .then((res) => {
          this.showFullPageLoading = false;
          this.makeToast(res.data.message, "success");
        })
        .catch((err) => {
          this.showFullPageLoading = false;
          this.makeToast(err.response.data.message, "danger");
        });
    },
  },
  computed: {
    receipts() {
      return this.$store.state.receipt.receipts;
    },
    receiptsCount() {
      return this.$store.state.receipt.receiptsCount;
    },
  },
  created() {
    this.loadingStatus = true;
    this.$store
      .dispatch("getReceipts")
      .then((res) => {
        this.loadingStatus = false;
      })
      .catch((err) => {
        this.loadingStatus = false;
        this.errorMessage = "Something went wrong";
      });
  },
};
</script>