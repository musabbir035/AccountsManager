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
              <b-form-group id="name-input-group" label="Name" label-for="name">
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
              name="email"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter an email address',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="name-input-group"
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
                  placeholder="Enter email address"
                ></b-form-input>

                <b-form-invalid-feedback id="email-live-feedback">
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

          <div class="col-12 col-lg-6" v-if="!user">
            <validation-provider
              name="password"
              :rules="{ required: true, min: 4 }"
              :custom-messages="{
                required: 'Please enter a password',
                min: 'Password must contain at least {length} characters',
              }"
              v-slot="validationContext"
              vid="confirmation"
            >
              <b-form-group
                id="password-input-group"
                label="Password"
                label-for="password"
              >
                <b-form-input
                  type="password"
                  id="password"
                  name="password"
                  v-model="form.password"
                  :state="getValidationState(validationContext)"
                  aria-describedby="password-live-feedback"
                  placeholder="Enter password"
                ></b-form-input>

                <b-form-invalid-feedback id="password-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <div class="col-12 col-lg-6" v-if="!user">
            <validation-provider
              name="passwordConfirm"
              rules="required:true|confirmed:confirmation"
              :custom-messages="{
                required: 'Please confirm the password',
                confirmed: 'Passwords do not match',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="password-confirm-input-group"
                label="Confirm Password"
                label-for="passwordConfirm"
              >
                <b-form-input
                  type="password"
                  id="passwordConfirm"
                  name="passwordConfirm"
                  v-model="form.passwordConfirm"
                  :state="getValidationState(validationContext)"
                  aria-describedby="password-confirm-live-feedback"
                  placeholder="Confirm password"
                ></b-form-input>

                <b-form-invalid-feedback id="password-confirm-live-feedback">
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
import LoadingSpinner from "../components/ui/LoadingSpinner.vue";

export default {
  components: { LoadingSpinner },
  props: ["user"],
  data() {
    return {
      form: {
        name: null,
        email: null,
        mobile: null,
        password: null,
        passwordConfrim: null,
        id: null,
      },
      errorMessage: null,
      btnText: "Add",
      showFullPageLoading: false,
    };
  },
  methods: {
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
    onSubmit() {
      this.showFullPageLoading = true;
      if (this.user) {
        this.form.id = this.user.id;
        this.$store
          .dispatch("editUser", this.form)
          .then((res) => {
            this.showFullPageLoading = false;
            this.$emit("user-add-update", res.data.message);
          })
          .catch((err) => {
            this.handleError(err);
          });
      } else {
        this.$store
          .dispatch("addUser", this.form)
          .then((res) => {
            this.showFullPageLoading = false;
            this.$emit("user-add-update", res.data.message);
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
    makeToast(msg, variant = null) {
      this.$bvToast.toast(msg, {
        autoHideDelay: 5000,
        variant: variant,
      });
    },
  },
  mounted() {
    if (this.user) {
      this.form.name = this.user.name;
      this.form.email = this.user.email;
      this.form.mobile = this.user.mobile;
      this.btnText = "Update";
    }
  },
};
</script>
