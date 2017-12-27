import { querystring } from 'vux'

const QUERY = querystring.parse(decodeURI(location.search))

export default {
  namespaced: true,
  state: {
    orderid: QUERY.openid, // 订单号
    license_number: QUERY.license_number // 车牌号
  },
  mutations: {},
  actions: {}
}