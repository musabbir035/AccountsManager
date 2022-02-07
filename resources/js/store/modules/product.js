const state = {
  products: [],
  productsCount: 0,
};

const actions = {
  async getAllProducts({ commit }) {
    return await axios
      .get('/api/products', {
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .then((res) => {
        commit('setAllProducts', res.data.products);
        commit('updateProductsCount', state.products.length);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
  async addProduct({ commit }, payload) {
    return await axios
      .post(
        '/api/products/store',
        {
          name: payload.name,
          address: payload.address,
          mobile: payload.mobile,
          balance: payload.balance,
        },
        {
          headers: {
            'Content-Type': 'application/json',
          },
        }
      )
      .then((res) => {
        commit('addProduct', res.data.product);
        commit('updateProductsCount', 1);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async editProduct({ commit }, payload) {
    return await axios
      .put(
        '/api/products/update/' + payload.id,
        {
          name: payload.name,
          address: payload.address,
          mobile: payload.mobile,
          balance: payload.balance,
        },
        {
          headers: {
            'Content-Type': 'application/json',
          },
        }
      )
      .then((res) => {
        commit('editProduct', res.data.product);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async deleteProduct({ commit }, product) {
    return await axios
      .get('/api/products/delete/' + product.id)
      .then((res) => {
        commit('deleteProduct', product);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
};

const mutations = {
  setAllProducts: (state, products) => (state.products = products),
  updateProductsCount: (state, count) => (state.productsCount += count),
  addProduct: (state, product) => state.products.push(product),
  editProduct: (state, product) => {
    const index = state.products.findIndex((u) => u.id == product.id);
    state.products.splice(index, 1, product);
  },
  deleteProduct: (state, product) => {
    const index = state.products.indexOf(product);
    state.products.splice(index, 1);
  },
};

export default {
  state,
  actions,
  mutations,
};
