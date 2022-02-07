import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home.vue";
import Login from "../views/auth/Login.vue";
import Users from "../views/Users.vue";
import Customers from "../views/customer/Customers.vue";
import CustomerDetails from "../views/customer/CustomerDetails.vue";
import Transactions from "../views/transaction/Transactions.vue";
import TransactionDetails from "../views/transaction/TransactionDetails.vue";
import Receipts from "../views/Receipts.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
    meta: { title: "Dashboard" },
  },
  {
    path: "/login",
    name: "LogIn",
    component: Login,
    meta: { title: "Log in" },
  },
  {
    path: "/users",
    name: "Users",
    component: Users,
    meta: { title: "Users" },
  },
  {
    path: "/customers",
    name: "Customers",
    component: Customers,
    meta: { title: "Customers" },
  },
  {
    path: "/customers/:id",
    name: "CustomerDetails",
    component: CustomerDetails,
    meta: { title: "Customer Details" },
  },
  // {
  //   path: '/products',
  //   name: 'Products',
  //   component: Products,
  //   meta: { title: 'Products' },
  // },
  // {
  //   path: "/receipts",
  //   name: "Receipts",
  //   component: Receipts,
  //   meta: { title: "Receipts" },
  // },
  {
    path: "/transactions",
    name: "Transactions",
    component: Transactions,
    meta: { title: "Transactions" },
  },
  {
    path: "/transactions/:id",
    name: "TransactionDetails",
    component: TransactionDetails,
    meta: { title: "Transaction Details" },
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

const DEFAULT_TITLE = "Accounts Manager";
router.afterEach((to, from) => {
  Vue.nextTick(() => {
    document.title = to.meta.title
      ? to.meta.title + " | " + DEFAULT_TITLE
      : DEFAULT_TITLE;
  });
});

//check if user is authenticated
router.beforeEach((to, from, next) => {
  if (to.name.toLowerCase() !== "login") {
    if (localStorage.getItem("at") !== "true") {
      next({
        path: "/login",
      });
    } else {
      next();
    }
  } else {
    if (localStorage.getItem("at") === "true") {
      next({
        path: "/",
      });
    } else {
      next();
    }
  }
  next();
});

export default router;
