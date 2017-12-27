import { $http } from '@/utils/fetch.js'
import { GetSign } from '@/utils'
/**
 * Vuex actions
 * 供vue页面的使用的async methods
 * 调用方式: $store.dispatch(MethodName, [arg])
 */

export default {
  delayExecute(delaytime = 300) { // 延迟执行某段代码
    return new Promise(resolve => setTimeout(() => resolve(), delaytime))
  },

  getCityList({ commit }) { // 获取当前开通的城市列表
    commit('SET_CITY_LIST')
    // $http.post('/index.php/Wechat/getCityList')
  },

  async getIds({ state, commit }) { // 获取openid
    const Q = state.QUERY
    const code = Q.code

    if (!Q.openid && Q.code) {
      state.source = 'wechat'
      let result = await $http.post('/index.php/Wechat/WeChat/getWechatOpenid', { code })
      if (result.code === 0) {
        commit('SET_OPENID', result.data)
      }
    }
  },

  async getCarInfoData(context, params) {
    return await $http.post('/index.php/Wechat/CarInfo/getCarInfo', params)
  },

  async getCarViolationData(context, params) {
    return await $http.post('/index.php/Wechat/CarViolation/getCarViolation', params)
  },

  /**
   * 获取违章列表
   * @param {Object} params { openid, license_number }
   */
  async getCarViolationList({ dispatch }, params) {
    const query = { ...params, sign: GetSign(params) }
    const { code, data } = await dispatch('getCarInfoData', query)
    if (code === 0) {
      let { vin, engineId } = data
      params = { ...params, vin, engineId }
      params = { ...params, sign: GetSign(params) }
      return await dispatch('getCarViolationData', params)
    }
  }
}