<template>
  <div>
    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
        <div class="row">
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
                ></b-form-input>

                <b-form-invalid-feedback id="name-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

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
                  max="11"
                  v-model="form.mobile"
                  :state="getValidationState(validationContext)"
                  aria-describedby="mobile-live-feedback"
                  placeholder="Enter mobile number"
                ></b-form-input>

                <b-form-invalid-feedback id="mobile-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="email"
              :rules="{ email: true }"
              :custom-messages="{
                email: 'Please enter a valid email address',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="email-input-group"
                label="Email Address"
                label-for="email"
              >
                <b-form-input
                  id="email"
                  name="email"
                  type="email"
                  v-model="form.email"
                  :state="getValidationState(validationContext)"
                  aria-describedby="email-live-feedback"
                  placeholder="Enter email address (optional)"
                ></b-form-input>

                <b-form-invalid-feedback id="email-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider name="address" v-slot="validationContext">
              <b-form-group
                id="name-input-group"
                label="Address"
                label-for="address"
              >
                <b-form-input
                  id="address"
                  name="address"
                  v-model="form.address"
                  :state="getValidationState(validationContext)"
                  aria-describedby="address-live-feedback"
                  placeholder="Enter address (optional)"
                ></b-form-input>

                <b-form-invalid-feedback id="address-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6">
            <validation-provider
              name="initial-balance"
              v-slot="validationContext"
            >
              <b-form-group
                id="initial-balance-input-group"
                label="Initial Balance"
                label-for="initial_balance"
                class="required"
              >
                <b-form-input
                  id="initial-balance"
                  name="initial-balance"
                  type="number"
                  v-model="form.initial_balance"
                  :state="getValidationState(validationContext)"
                  aria-describedby="initial-balance-live-feedback"
                  placeholder="Enter balance"
                ></b-form-input>

                <b-form-invalid-feedback id="initial-balance-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <!-- Balance type determines wheather owner will be paid or will need to pay
          the money (i.e. debit/credit). 1 for will be paid and the initial balance will
          be stored as positive whereas 2 for will need to pay and the initial balance
          will be stored as negative -->
          <div class="col-12 col-lg-6" v-if="!customer">
            <validation-provider name="type" v-slot="validationContext">
              <b-form-group
                id="type-input-group"
                label="Inital Balance Type"
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

          <div class="col-12 mb-3 text-danger" v-if="errorMessage">
            {{ errorMessage }}
          </div>
          <div class="col-12">
            <b-button type="submit" variant="success">{{ btnText }}</b-button>
          </div>
        </div>
      </b-form>
    </validation-observer>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
export default {
  props: ["customer"],
  data() {
    return {
      form: {
        name: null,
        address: null,
        email: null,
        mobile: null,
        initial_balance: null,
        id: null,
        type: null,
      },
      errorMessage: null,
      btnText: "Add",
      showFullPageLoading: false,
      typeOptions: [
        { value: null, text: "Select balance type" },
        { value: "1", text: "I will be paid" },
        { value: "2", text: "I need to pay" },
      ],
    };
  },
  methods: {
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
    onSubmit() {
      this.showFullPageLoading = true;
      if (this.customer) {
        this.form.id = this.customer.id;
        this.$store
          .dispatch("editCustomer", this.form)
          .then((res) => {
            this.showFullPageLoading = false;
            this.$emit("customer-added", res);
          })
          .catch((err) => {
            this.handleError(err);
          });
      } else {
        this.$store
          .dispatch("addCustomer", this.form)
          .then((res) => {
            this.showFullPageLoading = false;
            this.$emit("customer-added", res);
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
  },
  mounted() {
    if (this.customer) {
      this.form.name = this.customer.name;
      this.form.address = this.customer.address;
      this.form.mobile = this.customer.mobile;
      this.form.initial_balance = this.customer.initial_balance;
      this.form.type = this.customer.initial_balance < 0 ? 2 : 1;
      this.btnText = "Update";
    }
  },
};
</script>
