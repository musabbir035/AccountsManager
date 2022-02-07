const state = {
  transactions: [],
  transactionsByCustomer: [],
  transactionsCount: 0,
};

const getters = {
  getTransactionsByCustomer: (state) => (id) => {
    let abc = state.transactionsByCustomer.find((m) => m.customerId === id);
    console.log(state.transactionsByCustomer);
    console.log(
      state.transactionsByCustomer[0]
      //state.transactionsByCustomer.find(({ customerId }) => customerId === 1)
    );
    return state.transactionsByCustomer.find(
      ({ customerId }) => customerId === id
    );
  },
};

const actions = {
  async getTransactions({ commit }, id) {
    let url = '/api/transactions';
    if (id & !Number.isNaN(id)) {
      url = '/api/transactions?customerId=' + id;
    }
    return await axios
      .get(url)
      .then((res) => {
        commit('setAllTransactions', res.data.transactions);
        commit(
          'updateTransactionsCount',
          res.data.transactions ? res.data.transactions.length : 0
        );
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async addTransaction({ commit }, transaction) {
    return await axios
      .post('/api/transactions/store', transaction)
      .then((res) => {
        commit('addTransaction', res.data.transaction);
        commit('updateTransactionsCount');
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async editTransaction({ commit }, transaction) {
    return await axios
      .put('/api/transactions/update/' + transaction.id, transaction)
      .then((res) => {
        commit('editTransaction', res.data.transaction);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async deleteTransaction({ commit }, transaction) {
    return await axios
      .get('/api/transactions/delete/' + transaction.id)
      .then((res) => {
        commit('deleteTransaction', transaction);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },

  async generateLedger({ commit }, payload) {
    return await axios
      .post('/api/generate-ledger', {
        dateFrom: payload.dateFrom,
        dateTo: payload.dateTo,
        customerId: payload.customerId,
      })
      .then((res) => {
        //        commit('deleteTransaction', transaction);
        return res;
      })
      .catch((err) => {
        throw err;
      });
  },
};

const mutations = {
  setAllTransactions: (state, transactions) =>
    (state.transactions = transactions),

  setTansactionsByCustomer: (state, payload) => {
    const index = state.transactionsByCustomer
      .map((m) => m.customerId)
      .indexOf(payload.customerId);
    console.log(payload.customerId);
    if (index) {
      state.transactionsByCustomer[index] = payload;
    } else {
      state.transactionsByCustomer.push(payload);
    }
  },

  updateTransactionsCount: (state) => (state.transactionsCount += 1),

  addTransaction: (state, transaction) => state.transactions.push(transaction),

  editTransaction: (state, transaction) => {
    const index = state.transactions.findIndex((u) => u.id == transaction.id);
    state.transactions.splice(index, 1, transaction);
  },

  deleteTransaction: (state, transaction) => {
    const index = state.transactions.indexOf(transaction);
    state.transactions.splice(index, 1);
  },
};

export default {
  state,
  actions,
  mutations,
  getters,
};
