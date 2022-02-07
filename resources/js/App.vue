<template>
  <SplashScreen v-if="isLoading"></SplashScreen>
  <div v-else>
    <login v-if="$route.name === 'LogIn'" />
    <main-layout v-else />
  </div>
</template>

<script>
import MainLayout from "./components/layout/MainLayout.vue";
import SplashScreen from "./components/ui/SplashScreen.vue";
import Login from "./views/auth/Login.vue";

export default {
  components: {
    MainLayout,
    Login,
    SplashScreen,
  },
  data() {
    return {
      isLoading: true,
      user: null,
    };
  },
  mounted() {
    this.$store
      .dispatch("getUser")
      .then((res) => {
        this.isLoading = false;
        if (this.$route.name === "LogIn") {
          this.$router.push({
            name: "Home",
          });
        }
      })
      .catch((err) => {
        if (err.response.status === 401) {
          localStorage.removeItem("at");
          this.isLoading = false;
          if (this.$route.name !== "LogIn") {
            this.$router.push({
              name: "LogIn",
            });
          }
        }
      });
  },
  created() {
    if (localStorage.getItem("dark")) {
      document.body.classList.add("dark");
    }
  },
};
</script>
