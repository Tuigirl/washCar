// export { default as MUTATION } from ''
const SET_OPENID = 'SET_OPENID'
const SET_CITY_LIST = 'SET_CITY_LIST'

/**
 * Vuex mutations
 * 供vue页面的使用的sync methods
 * 调用方式: $store.commit(MethodName, [arg])
 */
export default {
  [SET_OPENID]: (state, openid) => state.openid = openid,

  [SET_CITY_LIST](state, city_list) {
    if (city_list) {
      state.city_list = city_list
    } else {
      state.city_list = [
        { "name": "北京", "value": "1" },
        { "name": "河北省", "value": "102" },
        { "name": "福建省", "value": "1310" },
        { "name": "北京市", "value": "2", "parent": "1" },
        { "name": "石家庄市", "value": "103", "parent": "102" },
        { "name": "唐山市", "value": "128", "parent": "102" },
        { "name": "厦门市", "value": "1326", "parent": "1310" }
      ]
    }
  }
}