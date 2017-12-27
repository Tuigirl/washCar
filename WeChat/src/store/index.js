import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import state from './state'
import getters from './getters'
import mutations from './mutations'
import actions from './actions'
import PrintDetailsData from './modules/PrintDetails'
import CarWashData from './modules/CarWash'
import CarLoanData from './modules/CarLoan'
import CarUsedData from './modules/CarUsed'
import CarInsuranceData from './modules/CarInsurance'
import VuxLoading from './modules/VuxLoading'

/**
 * 注册Vuex
 */
export default new Vuex.Store({
  state,
  getters,
  mutations,
  actions,
  modules: {
    PrintDetailsData,  // 电子打印单
    CarWashData,       // 快洗
    CarLoanData,       // 车贷页的 表单数据
    CarUsedData,       // 二手车页的 表单数据
    CarInsuranceData,  // 车险页
    VuxLoading         // 每个页的loading
  }
})