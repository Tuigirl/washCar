import { $http } from '@/utils/fetch.js'
import { querystring } from 'vux'

const QUERY = querystring.parse(decodeURI(location.search))

export default {
  namespaced: true,
  state: {
    orderid: null, // 订单号
    DeviceSerial: QUERY.DeviceSerial // 洗车机设备序列号
  },
  mutations: {},
  actions: {
    getOrderStatus({ state }, openid) {
      const promise = new Promise((resolve, reject) => {

        $http.post('/index.php/Wechat/WashCar/getOrderStatus', { openid })
          .then(result => {
            let status = true
            let data = result.data

            if (result.code === 0 &&
              data.washing === 0 &&
              data.time_left > 5 &&
              data.feedBack === '') { // 正在洗车进行中的订单 排除掉剩余5s的订单和已提交故障的订单
              state.orderid = data.order_id
            } else {
              status = false
            }
            resolve({status, data})
          })
          .catch(err => reject(err))
      })
      return promise
    }
  }
}