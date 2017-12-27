/* axios 基础配置 */
import Qs from 'qs'
import Axios from 'axios'
import { AlertModule } from 'vux'
import store from '@/store'

const Msg = msg => {
  AlertModule.show({
    title: '提示！',
    buttonText: '关闭',
    hideOnBlur: true,
    content: msg
  })
}
const ajax = Axios.create({
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  },
  timeout: 12000,
  responseType: 'json',
  transformRequest: [data => Qs.stringify(data)],
  transformResponse: [result => {
    if (result && !isNaN(result.code) && result.code !== 0)
      Msg(result.msg)
    return result
  }]
})

// 添加请求拦截器
ajax.interceptors.request.use(
  config => { // 在发送请求之前做些什么
    store.commit('updateLoading', true)
    return config
  },
  error => { // 对请求错误做些什么
    return Promise.reject(error)
  }
)


// 添加响应拦截器
ajax.interceptors.response.use(
  response => { // 对响应数据做点什么
    let result = response.data
    if (typeof result.data === 'string') {
      try {
        result.data = JSON.parse(result.data)
      } catch (error) {
        console.warn('->->Warn :: 参数解析失败', error)
      }
    }

    setTimeout(() => store.commit('updateLoading', false), 500)
    return result
  },
  error => { // 对响应错误做点什么
    if (error.response && error.response.status) {
      Msg(`服务器连接失败, 错误代码(${error.response.status})`)
    } else {
      Msg(String(error))
    }

    store.commit('updateLoading', false)
    return Promise.reject(error)
  }
)

export default {
  install(Vue) {
    Vue.prototype.$http = ajax
    Vue.http = ajax
  },
  $http: ajax
}

export const $http = ajax
