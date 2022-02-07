<template>
  <div>
    <b-navbar fixed="top">
      <a
        href="javascript:void(0)"
        class="nav-link sidebar-toggle"
        @click="sidebarToggle"
        v-b-tooltip.hover
        title="Toggle sidebar"
      >
        <b-icon icon="list"></b-icon>
      </a>

      <b-navbar-brand v-if="!isMobile" :to="{ name: 'Home' }">
        Accounts Manager
      </b-navbar-brand>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="ml-auto">
          <b-nav-item
            href="#"
            v-b-tooltip.hover
            :title="themeToggleBtnTitle"
            @click="themeToggle"
            class="mr-2"
          >
            <i class="far" :class="themeToggleBtnIcon"></i>
          </b-nav-item>
          <b-nav-item
            href="#"
            v-b-tooltip.hover
            title="Generate Ledger"
            @click="$emit('open-ledger-modal')"
            class="mr-2"
          >
            <i class="far fa-file-alt"></i>
          </b-nav-item>
          <b-nav-item-dropdown right no-caret>
            <template #button-content>
              <span v-b-tooltip.hover title="Account">
                <i class="far fa-user mr-1"></i>
                {{ authUserFirstName }}
              </span>
            </template>
            <b-dropdown-item href="#">Profile</b-dropdown-item>
            <b-dropdown-item href="#" @click="logout">Sign Out</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <b-sidebar
      id="sidebar-1"
      :shadow="isMobile"
      :visible="!isMobile"
      no-header
      :no-close-on-route-change="!isMobile"
      v-model="showSidebar"
    >
      <div v-if="isMobile" class="sidebar-brand">
        <a
          href="javascript:void(0)"
          class="link-icon nav-link"
          @click="sidebarToggle"
          v-b-tooltip.hover
          title="Toggle sidebar"
        >
          <b-icon icon="list"></b-icon>
        </a>
        <router-link to="/">Ledger Maker</router-link>
      </div>
      <sidebar-items :items="sidebarItems" />
    </b-sidebar>
    <loading-spinner :backdrop="true" v-if="showFullPageLoading" />
  </div>
</template>

<script>
import SidebarItems from "./SidebarItems.vue";
import LoadingSpinner from "../ui/LoadingSpinner.vue";

export default {
  components: {
    SidebarItems,
    LoadingSpinner,
  },
  props: ["layoutData"],
  data() {
    return {
      sidebarItems: [
        {
          title: "Dashboard",
          routeName: "Home",
          icon: "fas fa-tachometer-alt",
          exact: true,
        },
        {
          title: "Users",
          routeName: "Users",
          icon: "fas fa-users",
        },
        {
          title: "Customers",
          routeName: "Customers",
          icon: "fas fa-users",
        },
        {
          title: "Transactions",
          routeName: "Transactions",
          icon: "fas fa-exchange-alt",
        },
        // {
        //   title: "Receipts",
        //   routeName: "Receipts",
        //   icon: "fas fa-file-invoice",
        // },
      ],
      themeToggleBtnTitle: "",
      showSidebar: null,
      isMobile: null,
      mainMargin: null,
      themeToggleBtnIcon: "",
      showFullPageLoading: false,
    };
  },
  methods: {
    checkScreen() {
      this.windowWidth = window.innerWidth;
      if (this.windowWidth >= 768) {
        this.isMobile = false;
        this.showSidebar = true;
        this.$emit("change-margin", true);
      } else {
        this.isMobile = true;
        this.showSidebar = false;
        this.$emit("change-margin", false);
      }
    },
    sidebarToggle() {
      this.showSidebar = !this.showSidebar;
      if (this.showSidebar) {
        if (!this.isMobile) {
          this.$emit("change-margin", true);
        }
      } else {
        this.$emit("change-margin", false);
      }
    },
    themeToggle() {
      const body = document.body;
      if (body.classList.contains("dark")) {
        body.classList.remove("dark");
        localStorage.removeItem("dark");
        this.themeToggleBtnTitle = "Switch to dark mode";
        this.themeToggleBtnIcon = "fa-moon";
      } else {
        body.classList.add("dark");
        localStorage.setItem("dark", true);
        this.themeToggleBtnTitle = "Switch to light mode";
        this.themeToggleBtnIcon = "fa-sun";
      }
    },
    logout() {
      this.showFullPageLoading = true;
      this.$store.dispatch("logout").then((res) => {
        this.showFullPageLoading = false;
        localStorage.removeItem("at");
        this.$router.push({
          name: "LogIn",
        });
      });
    },
  },
  created() {
    this.checkScreen();
    window.addEventListener("resize", this.checkScreen);
    if (localStorage.getItem("dark")) {
      this.themeToggleBtnTitle = "Switch to light mode";
      this.themeToggleBtnIcon = "fa-sun";
    } else {
      this.themeToggleBtnTitle = "Switch to dark mode";
      this.themeToggleBtnIcon = "fa-moon";
    }
  },
  computed: {
    authUserFirstName() {
      if (this.$store.state.user.authUser) {
        return this.$store.getters.authUserFirstName;
      }
      return;
    },
  },
};
</script>

<style>
</style>