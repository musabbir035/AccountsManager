<template>
  <div>
    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
        <div class="row">
          <div class="col-12 col-lg-6">
            <validation-provider
              name="description"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter a description',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="description-input-group"
                label="Description"
                label-for="description"
                class="required"
              >
                <b-form-input
                  id="description"
                  name="description"
                  v-model="form.description"
                  :state="getValidationState(validationContext)"
                  aria-describedby="description-live-feedback"
                  placeholder="Enter description"
                ></b-form-input>

                <b-form-invalid-feedback id="description-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="type"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please select a type',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="type-input-group"
                label="Transaction Type"
                label-for="type"
                class="required"
              >
                <b-form-select
                  id="type"
                  name="type"
                  v-model="form.type"
                  :options="typeOptions"
                  :state="getValidationState(validationContext)"
                  aria-describedby="type-live-feedback"
                ></b-form-select>

                <b-form-invalid-feedback id="type-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="amount"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter an amount',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="amount-input-group"
                label="Transaction Amount"
                label-for="amount"
                class="required"
              >
                <b-form-input
                  id="amount"
                  name="amount"
                  type="number"
                  v-model="form.amount"
                  :state="getValidationState(validationContext)"
                  aria-describedby="amount-live-feedback"
                  placeholder="Enter transacion amount"
                ></b-form-input>

                <b-form-invalid-feedback id="amount-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="date"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter a date',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="date-input-group"
                label="Date"
                label-for="date"
                class="required"
              >
                <b-form-input
                  id="date"
                  name="date"
                  type="date"
                  v-model="form.date"
                  :state="getValidationState(validationContext)"
                  aria-describedby="date-live-feedback"
                  placeholder="Enter date"
                ></b-form-input>

                <b-form-invalid-feedback id="date-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="customer"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please select a customer',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="customer-input-group"
                label="Customer"
                label-for="customer"
                class="required"
              >
                <span @click="selectCustomer" style="display: block">
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

          <div class="col-12 mb-3 text-danger" v-if="errorMessage">
            {{ errorMessage }}
          </div>
          <div class="col-12">
            <b-button type="submit" variant="primary">{{ btnText }}</b-button>
          </div>
        </div>
      </b-form>
    </validation-observer>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
export default {
  props: ["transaction", "selectedCustomer"],
  data() {
    return {
      form: {
        description: null,
        amount: null,
        date: null,
        type: null,
        id: null,
        customerName: null,
        customerId: null,
      },
      errorMessage: null,
      btnText: "Add",
      typeOptions: [
        { value: null, text: "Select a type" },
        { value: "1", text: "Income" },
        { value: "2", text: "Expense" },
      ],
      showFullPageLoading: false,
    };
  },
  methods: {
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
    onSubmit() {
      this.showFullPageLoading = true;
      if (this.transaction) {
        this.$store
          .dispatch("editTransaction", {
            description: this.form.description,
            amount: this.form.amount,
            type: this.form.type,
            date: this.form.date,
            customer_id: this.form.customerId,
            id: this.transaction.id,
          })
          .then((res) => {
            this.showFullPageLoading = false;
            this.$emit("transaction-added-edited", res);
          })
          .catch((err) => {
            this.handleError(err);
          });
      } else {
        this.$store
          .dispatch("addTransaction", {
            description: this.form.description,
            amount: this.form.amount,
            type: this.form.type,
            date: this.form.date,
            customer_id: this.form.customerId,
          })
          .then((res) => {
            this.$emit("transaction-added-edited", res);
          })
          .catch((err) => {
            this.handleError(err);
          });
      }
    },
    handleError(err) {
      this.showFullPageLoading = false;
      if (err.response.data.errors) {
        this.$refs.observer.setErrors(err.response.data.errors);
      } else {
        this.errorMessage = err.response.data.message;
      }
    },
    selectCustomer() {
      this.$emit("open-search");
    },
  },
  watch: {
    selectedCustomer: function (newVal, oldVal) {
      this.form.customerName = newVal.name;
      this.form.customerId = newVal.id;
    },
  },
  mounted() {
    if (this.transaction) {
      this.form.amount = this.transaction.amount;
      this.form.description = this.transaction.description;
      this.form.date = this.dateFormat(this.transaction.date);
      this.form.type = this.transaction.type_id;
      this.btnText = "Update";
      if (this.transaction.customer) {
        this.form.customerName = this.transaction.customer.name;
        this.form.customerId = this.transaction.customer.id;
      }
    }
  },
};
</script>
