/* Axios 基础配置 */

let MyPlugin = {}
import Qs from 'qs'
import Axios from 'axios'
import { Message } from 'element-ui'

let http = Axios.create({
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  timeout: 12000,
  responseType: 'json',
  transformRequest: [params => {
    return Qs.stringify(params)
  }],
  transformResponse: [response => {
    if (response && !isNaN(response.code) && response.code !== 0) {
      Message.error(response.msg || response.info)
    }
    return response
  }]
})

// 添加请求拦截器
http.interceptors.request.use((config) => {
  // 在发送请求之前做些什么
  return config
}, (error) => {
  // 对请求错误做些什么
  if (error.response && error.response.status) {
    Message.error(`请求服务器失败, 错误代码(${error.response.status})`)
  }
  return Promise.reject(error)
})

// 添加响应拦截器
http.interceptors.response.use((response) => {
  // 对响应数据做点什么
  return response.data
}, (error) => {
  // 对响应错误做点什么
  let errMsg = ''
  if (error.response && error.response.status) {
    errMsg = `服务器连接失败, 错误代码(${error.response.status})`
  } else {
    errMsg = String(error)
  }
  Message.error(errMsg)
  return Promise.reject(error)
})

MyPlugin.install = function (Vue, options) {
  Vue.prototype.$http = http
}

export default MyPlugin

export const $http = http