import { querystring } from 'vux'

const QUERY = querystring.parse(decodeURI(location.search))

/* Vuex state */
export default {
  QUERY,
  host_name: 'https://washcar.caryu.com/',
  isAndroid: navigator.userAgent.match(/android/i),
  isIOS: navigator.userAgent.match(/(iPhone|iPod|iPad);?/i),
  clientH: document.body.clientHeight,
  source: 'print_file', // 信息来源 print_file:电子打印单 wechat:公众号按钮
  openid: QUERY.openid, // 微信的用户ID
  open_city: { // 当前开通城市{128:唐山, 1326:厦门, 2:北京},空数组时表示全部
    CarLoan: [],
    CarUsed: [],
    CarInsurance: []
  },
  city_list: [] // 城市数据
}