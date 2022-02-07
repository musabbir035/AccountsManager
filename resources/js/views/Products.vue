<template>
  <div>
    <div class="row mb-2">
      <div class="col-6">
        <h1 class="page-header">Products</h1>
      </div>
      <div class="col-6 text-right">
        <b-button variant="primary" v-b-modal.addEditModal>
          Add New Product
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
                placeholder="Type to search products..."
              ></b-form-input>
            </b-input-group>
          </b-col>
        </b-row>

        <b-table
          striped
          :items="products"
          :fields="tableFields"
          :filter="filter"
          :current-page="currentPage"
          :per-page="perPage"
          stacked="sm"
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

    <b-row class="mt-4" v-if="$store.state.product.productsCount > perPage">
      <b-col sm="7" md="6" lg="4" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="productsCount"
          :per-page="perPage"
          align="fill"
          size="sm"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>

    <b-modal
      id="addEditModal"
      title="Add Product"
      size="lg"
      hide-footer
      no-close-on-backdrop
      scrollable
      @hidden="resetModal"
    >
      <product-add-edit-form
        :product="beingEditedProduct"
        @add-product="onAddProduct"
      />
    </b-modal>

    <b-modal
      id="detailsModal"
      title="Product Details"
      size="md"
      hide-footer
      no-close-on-backdrop
    >
      <item-details :detailsData="productData" />
    </b-modal>

    <b-modal
      id="deleteModal"
      title="Delete Product"
      size="sm"
      no-close-on-backdrop
      centered
      header-bg-variant="danger"
      @hidden="resetModal"
    >
      <div>
        Are you sure you want to delete this product:
        {{ beingEditedProduct ? beingEditedProduct.name : "" }}
      </div>
      <template #modal-footer>
        <div class="w-100 text-right">
          <b-button variant="danger" size="sm" @click="deleteProduct">
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
import ProductAddEditForm from "../components/ProductAddEditForm.vue";
import ItemDetails from "../components/ItemDetails.vue";

export default {
  components: {
    LoadingSpinner,
    ProductAddEditForm,
    ItemDetails,
  },
  data() {
    return {
      tableFields: [
        {
          key: "name",
          sortable: true,
        },
        {
          key: "unit",
          sortable: true,
        },
        {
          key: "price",
          sortable: true,
        },
        {
          key: "discount_price",
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
      productData: null,
      beingEditedProduct: null,
      loadingStatus: null,
      errorMessage: null,
    };
  },
  methods: {
    resetModal() {
      this.beingEditedProduct = null;
    },
    details(item, index, event) {
      this.productData = item;
      this.$bvModal.show("detailsModal");
    },
    openEditModal(item, index, event) {
      this.beingEditedProduct = item;
      this.$bvModal.show("addEditModal");
    },
    openDeleteModal(item, index, event) {
      this.beingEditedProduct = item;
      this.$bvModal.show("deleteModal");
    },
    onAddProduct(e) {
      this.showFullPageLoading = e.loading;
      if (e.success) {
        this.hideModal("addEditModal");
        this.makeToast(e.message, "success");
      }
    },
    deleteProduct() {
      this.hideModal("deleteModal");
      this.showFullPageLoading = true;
      this.$store
        .dispatch("deleteProduct", this.beingEditedProduct)
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
    products() {
      return this.$store.state.product.products;
    },
    productsCount() {
      return this.$store.state.product.productsCount;
    },
  },
  created() {
    this.loadingStatus = true;
    this.$store
      .dispatch("getAllProducts")
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