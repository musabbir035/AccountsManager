<template>
  <div>
    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
        <div class="row">
          <div class="col-12 col-lg-6">
            <validation-provider name="customer" v-slot="validationContext">
              <b-form-group
                id="customer-input-group"
                label="Customer"
                label-for="customer"
              >
                <span @click="$emit('open-search')" style="display: block">
                  <b-form-input
                    id="customer"
                    name="customer"
                    v-model="form.customerName"
                    :state="getValidationState(validationContext)"
                    aria-describedby="customer-live-feedback"
                    placeholder="Select a customer"
                    disabled
                  ></b-form-input>
                </span>

                <b-form-invalid-feedback id="customer-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div class="col-12 col-lg-6">
            <validation-provider
              name="date-from"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter start date',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="date-from-input-group"
                label="Date From"
                label-for="date-from"
                class="required"
              >
                <b-form-input
                  id="date-from"
                  name="date-from"
                  type="date"
                  v-model="form.dateFrom"
                  :state="getValidationState(validationContext)"
                  aria-describedby="date-from-live-feedback"
                  placeholder="Enter start date"
                ></b-form-input>

                <b-form-invalid-feedback id="date-from-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div class="col-12 col-lg-6">
            <validation-provider
              name="date-to"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter end date',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="date-to-input-group"
                label="Date to"
                label-for="date-to"
                class="required"
              >
                <b-form-input
                  id="date-to"
                  name="date-to"
                  type="date"
                  v-model="form.dateTo"
                  :state="getValidationState(validationContext)"
                  aria-describedby="date-to-live-feedback"
                  placeholder="Enter end date"
                ></b-form-input>

                <b-form-invalid-feedback id="date-to-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>
          <div class="col-12 mb-3 text-danger" v-if="errorMessage">
            {{ errorMessage }}
          </div>
          <div class="col-12">
            <b-button type="submit" variant="primary">Generate</b-button>
          </div>
        </div>
      </b-form>
    </validation-observer>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import LoadingSpinner from "../ui/LoadingSpinner.vue";

export default {
  props: ["selectedCustomer"],
  components: {
    LoadingSpinner,
  },
  data() {
    return {
      form: {
        customerName: null,
        dateFrom: null,
        dateTo: null,
        customerId: null,
      },
      errorMessage: null,
      showFullPageLoading: false,
    };
  },
  methods: {
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
    onSubmit() {
      this.showFullPageLoading = true;
      this.$store
        .dispatch("generateLedger", this.form)
        .then((res) => {
          this.showFullPageLoading = false;
          this.$emit("ledgerGenerated", res);
        })
        .catch((err) => {
          this.handleError(err);
        });
    },
    handleError(err) {
      this.showFullPageLoading = false;
      if (err.response.data.errors) {
        this.$refs.observer.setErrors(err.response.data.errors);
      } else {
        this.errorMessage = err.response.data.message;
      }
    },
  },
  watch: {
    selectedCustomer: function (newVal, oldVal) {
      this.form.customerName = newVal.name;
      this.form.customerId = newVal.id;
    },
  },
};
</script>

<style>
</style>