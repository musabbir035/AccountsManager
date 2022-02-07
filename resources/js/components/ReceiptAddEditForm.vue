<template>
  <div>
    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
        <div class="row">
          <h5 class="col-12">Customer Info</h5>
          <div class="col-12 col-lg-6">
            <validation-provider
              name="mobile"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter a mobile number',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="mobile-input-group"
                label="Mobile Number"
                label-for="mobile"
                class="required"
              >
                <b-form-input
                  id="mobile"
                  name="mobile"
                  v-model="form.mobile"
                  :state="getValidationState(validationContext)"
                  aria-describedby="mobile-live-feedback"
                  placeholder="Enter mobile number"
                  @input="setCustomer"
                ></b-form-input>

                <b-form-invalid-feedback id="mobile-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div class="col-12 col-lg-6">
            <validation-provider
              name="name"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter a name',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="name-input-group"
                label="Name"
                label-for="name"
                class="required"
              >
                <b-form-input
                  id="name"
                  name="name"
                  v-model="form.name"
                  :state="getValidationState(validationContext)"
                  aria-describedby="name-live-feedback"
                  placeholder="Enter name"
                  :disabled="isDisabled"
                ></b-form-input>

                <b-form-invalid-feedback id="name-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div class="col-12 col-lg-6">
            <validation-provider
              name="address"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter an address',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="address-input-group"
                label="Address"
                label-for="address"
                class="required"
              >
                <b-form-input
                  id="address"
                  name="address"
                  v-model="form.address"
                  :state="getValidationState(validationContext)"
                  aria-describedby="address-live-feedback"
                  placeholder="Enter address"
                  :disabled="isDisabled"
                ></b-form-input>

                <b-form-invalid-feedback id="address-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <h5 class="col-12">Items</h5>
          <a href="javascript:void(0)" class="col-12" @click="addItem">
            Add new item
          </a>
          <component
            v-for="(form, index) in receiptItemForms"
            :key="index"
            :is="form"
          />

          <div class="col-12 mb-3 text-danger" v-if="errorMessage">
            {{ errorMessage }}
          </div>
          <div class="col-12">
            <b-button type="submit" variant="primary">Add</b-button>
          </div>
        </div>
      </b-form>
    </validation-observer>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import ReceiptItemForm from "./ReceiptItemForm.vue";

export default {
  components: {
    ReceiptItemForm,
  },
  data() {
    return {
      form: {
        customerId: null,
        mobile: null,
        name: null,
        address: null,
        dueAmount: null,
      },
      customer: null,
      isDisabled: false,
      showFullPageLoading: false,
      errorMessage: null,
      receiptItemForms: [],
    };
  },
  methods: {
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
    setCustomer() {
      if (this.form.mobile && this.form.mobile.length === 11) {
        this.showFullPageLoading = true;
        this.$store
          .dispatch("getCustomer", { getter: this.form.mobile, type: "mobile" })
          .then((res) => {
            this.customer = res.data.customer ? res.data.customer : null;
            this.form.customerId = res.data.customer
              ? res.data.customer.id
              : null;
            this.form.name = res.data.customer ? res.data.customer.name : null;
            this.form.address = res.data.customer
              ? res.data.customer.address
              : null;
            this.showFullPageLoading = false;
            this.isDisabled = true;
          })
          .catch((err) => {});
      } else {
        this.customer = null;
        this.customerId = null;
        this.form.name = null;
        this.form.address = null;
        this.isDisabled = false;
      }
    },
    addItem() {
      this.receiptItemForms.push("ReceiptItemForm");
    },
    onsubmit() {},
  },
};
</script>

<style>
</style>