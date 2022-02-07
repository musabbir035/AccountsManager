<template>
  <div>
    <div class="row mb-2">
      <div class="col-6">
        <h1 class="h2">Users</h1>
      </div>
      <div class="col-6 text-right">
        <b-button variant="primary" v-b-modal.addEditModal>
          Add New User
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
                placeholder="Type to search users..."
              ></b-form-input>
            </b-input-group>
          </b-col>
        </b-row>

        <b-table
          striped
          :items="users"
          :fields="tableFields"
          :filter="filter"
          :current-page="currentPage"
          :per-page="perPage"
          stacked="sm"
          :tbody-tr-class="inactiveClass"
        >
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
              v-if="row.item.id == $store.state.user.authUser.id"
              size="xs"
              @click="openEditModal(row.item, row.index, $event.target)"
              class="mr-1 mt-1"
              variant="warning"
            >
              Edit
            </b-button>
            <b-button
              v-if="
                $store.state.user.authUser.role_id == 1 && row.item.role_id != 1
              "
              size="xs"
              @click="openStatusUpdateModal(row.item, row.index, $event.target)"
              class="mt-1"
              :variant="row.item.deleted_at ? 'success' : 'danger'"
            >
              {{ row.item.deleted_at ? "Activate" : "Deactivate" }}
            </b-button>
          </template>
        </b-table>
      </div>
    </b-card>

    <b-row class="mt-4" v-if="$store.state.user.usersCount > perPage">
      <b-col sm="7" md="6" lg="4" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="usersCount"
          :per-page="perPage"
          align="fill"
          size="sm"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>

    <b-modal
      id="addEditModal"
      :title="addEditModalTitle"
      size="lg"
      hide-footer
      no-close-on-backdrop
      scrollable
      @hidden="resetModal"
    >
      <user-add-form
        :user="beingEditedUser"
        @user-add-update="onUserAddedUpdated"
      />
    </b-modal>

    <b-modal
      id="detailsModal"
      title="User Details"
      size="md"
      hide-footer
      no-close-on-backdrop
    >
      <item-details :detailsData="userData" />
    </b-modal>

    <b-modal
      id="statusUpdateModal"
      title="Deactivate User"
      size="sm"
      no-close-on-backdrop
      centered
      header-bg-variant="danger"
      @hidden="resetModal"
    >
      <div>{{ statusAlertMessage }}</div>
      <template #modal-footer>
        <div class="w-100 text-right">
          <b-button variant="danger" size="sm" @click="updateUserStatus">
            Yes
          </b-button>
          <b-button
            variant="secondary"
            size="sm"
            @click="hideModal('statusUpdateModal')"
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
import UserAddForm from "../components/forms/UserAddEditForm.vue";
import ItemDetails from "../components/ItemDetails.vue";

export default {
  components: { LoadingSpinner, UserAddForm, ItemDetails },
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
          key: "email",
          sortable: true,
        },
        {
          key: "mobile",
          label: "Mobile Number",
          sortable: true,
        },
        {
          key: "role",
          sortable: true,
        },
        {
          key: "actions",
        },
      ],
      filter: null,
      currentPage: 1,
      perPage: 10,
      userData: null,
      beingEditedUser: null,
      showFullPageLoading: false,
      statusAlertMessage: "",
      loadingStatus: null,
      errorMessage: null,
      addEditModalTitle: "Add User",
    };
  },
  methods: {
    resetModal() {
      this.beingEditedUser = null;
      this.addEditModalTitle = "Add User";
    },
    details(item, index, event) {
      this.userData = item;
      this.$bvModal.show("detailsModal");
    },
    openEditModal(item, index, event) {
      this.beingEditedUser = item;
      this.addEditModalTitle = "Edit User";
      this.$bvModal.show("addEditModal");
    },
    openStatusUpdateModal(item, index, event) {
      this.statusAlertMessage =
        "Are you sure you want to deactivate user: " + item.name;
      if (item.deleted_at) {
        this.statusAlertMessage =
          "Are you sure you want to activate user: " + item.name;
      }
      this.beingEditedUser = item;
      this.$bvModal.show("statusUpdateModal");
    },
    updateUserStatus() {
      this.hideModal("statusUpdateModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("updateUserActiveStatus", this.beingEditedUser)
        .then((res) => {
          this.showFullPageLoading = false;
          this.makeToast(res.data.message, "success");
        })
        .catch((err) => {
          this.showFullPageLoading = false;
          this.makeToast(err.response.data.message, "danger");
        });
    },
    onUserAddedUpdated(msg) {
      this.hideModal("addEditModal");
      this.makeToast(msg, "success");
    },
    inactiveClass(item, type) {
      if (!item || type !== "row") return;
      if (item.deleted_at) return "table-danger";
    },
  },
  computed: {
    users() {
      return this.$store.state.user.users;
    },
    usersCount() {
      return this.$store.state.user.usersCount;
    },
  },
  created() {
    this.loadingStatus = true;
    this.$store
      .dispatch("getAllUsers")
      .then((res) => {
        this.loadingStatus = false;
      })
      .catch((err) => {
        this.loadingStatus = false;
        if (this.usersCount <= 0) {
          this.errorMessage = "Something went wrong";
        }
      });
  },
};
</script>
