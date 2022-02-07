<template>
  <div class="login-card">
    <div class="text-center">
      <h1>{{ $appName }}</h1>
      <p>Log in to your account</p>
    </div>
    <div class="card">
      <div class="card-body p-4">
        <validation-observer ref="observer" v-slot="{ handleSubmit }">
          <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
            <validation-provider
              name="username"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter your email or mobile number',
              }"
              v-slot="validationContext"
            >
              <b-form-group
                id="email-input-group"
                label="Email or Mobile Number"
                label-for="username"
              >
                <b-form-input
                  id="username"
                  name="username"
                  v-model="form.username"
                  :state="getValidationState(validationContext)"
                  aria-describedby="username-live-feedback"
                  placeholder="Enter email or mobile number"
                ></b-form-input>

                <b-form-invalid-feedback id="username-live-feedback">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <validation-provider
              name="password"
              :rules="{ required: true }"
              :custom-messages="{
                required: 'Please enter your password',
              }"
              v-slot="validationContext"
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

            <b-form-group id="input-group-4">
              <b-form-checkbox
                v-model="form.remember"
                :value="true"
                :unchecked-value="false"
              >
                Remember log in
              </b-form-checkbox>
            </b-form-group>

            <div class="mb-3 text-danger" v-if="authError">
              {{ authError }}
            </div>
            <b-button type="submit" variant="primary">Log in</b-button>
          </b-form>
        </validation-observer>
      </div>
    </div>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import LoadingSpinner from "../../components/ui/LoadingSpinner.vue";

export default {
  components: { LoadingSpinner },
  data() {
    return {
      form: {
        username: null,
        password: null,
        remember: null,
      },
      authError: null,
      showFullPageLoading: false,
    };
  },
  methods: {
    onSubmit() {
      this.showFullPageLoading = true;
      this.$store
        .dispatch("login", this.form)
        .then((res) => {
          this.showFullPageLoading = false;
          this.$router.push("/");
        })
        .catch((err) => {
          this.showFullPageLoading = false;
          if (err.response.data.errors) {
            this.$refs.observer.setErrors(err.response.data.errors);
          } else {
            this.authError = err.response.data.message;
          }
        });
    },
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },
  },
};
</script>
