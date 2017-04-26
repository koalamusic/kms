/*eslint camelcase: ["error", {properties: "never"}]*/

import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const searchStore = new Vuex.Store({
    state: {
        query: ''
    },
    actions: {
        CHANGE_QUERY ({ commit }, query) {
            commit('SET_QUERY', { query: query })
        },
    },
    mutations: {
        SET_QUERY: (state, { query }) => {
            state.query = query
        },
    },
    getters: {
    },
    modules: {

    },
})