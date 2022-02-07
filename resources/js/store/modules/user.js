import axios from "axios";

const state = {
  users: [],
  usersCount: 0,
  authUser: null,
  errorMessage: null,
  successMessage: null,
};

const getters = {
  authUserFirstName: (state) => {
    if (state.authUser) {
      return state.authUser.name.split(" ")[0];
    }
  },
};

const actions = {
  async login({ commit }, payload) {
    return axios.get("/sanctum/csrf-cookie").then((response) => {
      return axios
        .post("/login", payload)
        .then((res) => {
          localStorage.setItem("at", "true");
          commit("setAuthUser", res.data.user);
          return res;
        })
        .catch((err) => {
          throw err;
        });
    });
  },

  async logout({ commit }) {
    return await axios
      .get("/logout")
      .then((res) => {
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async getAllUsers({ commit }) {
    return await axios
      .get("/api/users", {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        commit("setAllUsers", res.data.users);
        commit("updateUsersCount", state.users.length);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async getUser({ commit }) {
    return await axios
      .get("/api/user", {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        commit("setAuthUser", res.data.user);
        return res;
      })
      .catch((err) => {
        if (err.response.status === 401) {
          commit("setAuthUser", null);
        }
        throw err;
      });
  },

  async addUser({ commit }, payload) {
    return await axios
      .post(
        "/api/users/store",
        {
          name: payload.name,
          email: payload.email,
          password: payload.password,
          passwordConfirm: payload.passwordConfirm,
          mobile: payload.mobile,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then((res) => {
        commit("addUser", res.data.user);
        commit("updateUsersCount", 1);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async editUser({ commit }, payload) {
    return await axios
      .put(
        "/api/users/update/" + payload.id,
        {
          name: payload.name,
          email: payload.email,
          mobile: payload.mobile,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then((res) => {
        commit("editUser", res.data.user);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async updateUserActiveStatus({ commit }, user) {
    return await axios
      .get("/api/users/update-status/" + user.id, {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((res) => {
        commit("editUser", res.data.user);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async delete({ commit }, user) {
    return await axios
      .get("/api/users/delete/" + user.id)
      .then((res) => {
        commit("deleteUser", user);
        commit("updateUsersCount", -1);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
};

const mutations = {
  setAllUsers: (state, users) => (state.users = users),
  setAuthUser: (state, user) => (state.authUser = user),
  errorMessage: (state, msg) => (state.errorMessage = msg),
  addUser: (state, user) => state.users.push(user),
  successMessage: (state, msg) => (state.successMessage = msg),
  updateUsersCount: (state, count) => (state.usersCount += count),
  deleteUser: (state, user) => {
    const index = state.users.indexOf(user);
    state.users.splice(index, 1);
  },
  editUser: (state, user) => {
    const index = state.users.findIndex((u) => u.id == user.id);
    state.users.splice(index, 1, user);
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
