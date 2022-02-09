const state = {
  customers: [],
  customer: null,
  customersCount: 0,
};

const actions = {
  async getAllCustomers({ commit }, query) {
    return await axios
      .post("/api/customers", {
        query: query,
      })
      .then((res) => {
        commit("setAllCustomers", res.data.customers);
        commit(
          "updateCustomersCount",
          state.customers ? state.customers.length : 0
        );
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async getCustomer({ commit }, { getter, type }) {
    return await axios
      .get("/api/customer?" + type + "=" + getter) //url example: /api/customer?id=1
      .then((res) => {
        commit("setCustomer", res.data.customer);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async addCustomer({ commit }, payload) {
    return await axios
      .post("/api/customers/store", payload, {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        commit("addCustomer", res.data.customer);
        commit("updateCustomersCount", 1);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async editCustomer({ commit }, payload) {
    return await axios
      .put(
        "/api/customers/update/" + payload.id,
        {
          name: payload.name,
          address: payload.address,
          mobile: payload.mobile,
          balance: payload.balance,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then((res) => {
        commit("editCustomer", res.data.customer);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async deleteCustomer({ commit }, customer) {
    return await axios
      .get("/api/customers/delete/" + customer.id)
      .then((res) => {
        commit("deleteCustomer", customer);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
};

const mutations = {
  setAllCustomers: (state, customers) => (state.customers = customers),
  setCustomer: (state, customer) => (state.customer = customer),
  updateCustomersCount: (state, count) => (state.customersCount += count),
  addCustomer: (state, customer) => state.customers.push(customer),
  editCustomer: (state, customer) => {
    const index = state.customers.findIndex((u) => u.id == customer.id);
    state.customers.splice(index, 1, customer);
  },
  deleteCustomer: (state, customer) => {
    const index = state.customers.indexOf(customer);
    state.customers.splice(index, 1);
  },
};

export default {
  state,
  actions,
  mutations,
};
