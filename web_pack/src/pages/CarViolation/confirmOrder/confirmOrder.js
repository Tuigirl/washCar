import '@/assets/css/api/CarViolation/violationList.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'
import { GetUrlRequest } from '@/util.js'

var device = GetUrlRequest().device
var code = GetUrlRequest().code
var openid = GetUrlRequest().openid
var license_number = GetUrlRequest().license_number

document.addEventListener("DOMContentLoaded", () => {

  var items = {}
  var data = document.querySelector('#data');
  var illegal_info = JSON.parse(data.innerText);

  items.illegal_info = illegal_info
  items.license_number = license_number

  if (items.license_number) {/*车牌号样式处理*/
    items.license_number = items.license_number.substr(0, 1) + '·' + items.license_number.substr(1, items.license_number.length - 1);
  }
  /**
   * 违章 确认支付加载器
   */
  var vm = new Vue({
    el: '#all-box',
    data: {
      items: items || new Array()//包含渲染的数据
    },
    computed: {
      payment_amount() {//支付总金额 罚款+服务费
        return items.illegal_info.map(v => Number(v.count) || 0).reduce((prev, next) => prev + next)
          + items.illegal_info.map(v => Number(v.Poundage) || 0).reduce((prev, next) => prev + next)
      }
    },
    methods: {/*绑定的一些方法*/
      //点击 查看违章详情
      ViewDetails: function (code) {
        sessionStorage.is_support_online_pay = 'yes';
        $.JumpURL('/Xiaochengxu/CarViolation/violationDetail', {
          device,
          code,
          openid,
          license_number,
        })
      },
      //点击 确认支付
      clk_submit: function () {
        $.post(
          '/Xiaochengxu/CarViolation/toPay'
          , {
            device,
            code,
            openid,
            license_number
          }
          , (res) => {
            if (res.code === 0) {
              const orderid = res.data.data.orderId
              if (window.__wxjs_environment === 'miniprogram') { // 小程序里
                let payParams = encodeURIComponent(JSON.stringify(res.data.data)) // 请求微信支付要携带的参数
                let jumpURL = encodeURIComponent(`https://washcar.caryu.com/Xiaochengxu/CarViolation/paySuccess?orderid=${orderid}`) // 在小程序里支付成功后要跳转的页面
                let url = `/pages/ToPay?payParams=${payParams}&jumpURL=${jumpURL}`
                wx.miniProgram.navigateTo({ url }) // 跳小程序页去发起支付
              }
            } else {
              layer.open({ content: res.msg, time: 2 });
            }
          }
          , 'json')
      }
    }
  });//vm---end

  window.vm = vm;//用于调试
})
