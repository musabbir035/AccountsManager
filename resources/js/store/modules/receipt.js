const state = {
  receipts: [],
  receiptsCount: 0,
};

const actions = {
  async getReceipts({ commit }, id) {
    let url = '/api/receipts';
    if (id & !Number.isNaN(id)) {
      url = '/api/receipts?customerId=' + id;
    }
    return await axios
      .get(url)
      .then((res) => {
        commit('setAllReceipts', res.data.receipts);
        commit(
          'updateReceiptsCount',
          res.data.receipts ? res.data.receipts.length : 0
        );
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async addReceipt({ commit }, receipt) {
    return await axios
      .post('/api/receipts/store', receipt)
      .then((res) => {
        commit('addReceipt', res.data.receipt);
        commit('updateReceiptsCount');
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async editReceipt({ commit }, receipt) {
    return await axios
      .put('/api/receipts/update/' + receipt.id, receipt)
      .then((res) => {
        commit('editReceipt', res.data.receipt);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async deleteReceipt({ commit }, receipt) {
    return await axios
      .get('/api/receipts/delete/' + receipt.id)
      .then((res) => {
        commit('deleteReceipt', receipt);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
};

const mutations = {
  setAllReceipts: (state, receipts) => (state.receipts = receipts),

  updateReceiptsCount: (state, count) => {
    if (count && Number.isNaN(count)) {
      state.receiptsCount = count;
    } else {
      state.receiptsCount += 1;
    }
  },

  addReceipt: (state, receipt) => state.receipts.push(receipt),

  editReceipt: (state, receipt) => {
    const index = state.receipts.findIndex((u) => u.id == receipt.id);
    state.receipts.splice(index, 1, receipt);
  },

  deleteReceipt: (state, receipt) => {
    const index = state.receipts.indexOf(receipt);
    state.receipts.splice(index, 1);
  },
};

export default {
  state,
  actions,
  mutations,
};
