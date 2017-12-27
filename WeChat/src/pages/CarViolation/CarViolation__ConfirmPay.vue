<template>
  <section id="confirmOrder">
    <Scroller ref="myScroller" height="-54" lock-x scrollbar-y>
      <transition-group tag="ul" id="wrapper" class="con" appear appear-active-class="animated flipInX">
        <li v-for="item in items" v-bind:key="item.SecondaryUniqueCode" @click="ViewDetails(item.SecondaryUniqueCode)">
          <h2>{{ license_number | f_license_number }}</h2>
          <section>
            <div class="address">{{ '['+item.LocationName+']' }} {{ item.Location }}</div>
            <div class="time">
              <span class="c-A1">时间：</span>{{ item.Time }}</div>
            <div class="reason">
              <span class="c-A1">原因：</span>
              <span class="reason-s">{{ item.Reason }}</span>
              <span class="arrow">
                <!-- <img src="../../assets/img/api/arrow_right.png"> -->
              </span>
            </div>
            <div class="toll">
              <span class="c-A1">办理服务费：</span>{{ '¥ '+(item.Poundage||'0.00') }}
            </div>
            <div class="fine">
              <span class="c-A1">罚款：</span>{{ '¥ '+item.count||'0.00' }}
              <!-- <span class="c-A1">扣分：</span>{{ (item.Degree||0)+' 分' }} -->
            </div>
          </section>
        </li>
      </transition-group>
    </Scroller>

    <ul class="foot_button">
      <li>共计： ¥{{ payment_amount }}元</li>
      <li role="button" @click="clk_submit">支付</li>
    </ul>
  </section>
</template>

<script>
import '@/assets/css/api/common.css'
import '@/assets/css/api/CarViolation/violationList.css'
import { Scroller } from 'vux'
import { WxPay } from '@/utils'
import { mapState } from 'vuex'

let orderid = null

export default {
  name: 'CarViolation__ConfirmPay',

  components: { Scroller },

  data() {
    let { code, openid, device, license_number } = this.$route.query
    return {
      code,
      openid,
      device,
      license_number,
      items: []
    }
  },

  computed: {
    ...mapState(['source']),
    payment_amount() {//支付总金额 罚款+服务费
      if (!this.items.length) return 0
      return this.items.map(v => Number(v.count) || 0).reduce((prev, next) => prev + next)
        + this.items.map(v => Number(v.Poundage) || 0).reduce((prev, next) => prev + next)
    }
  },

  filters: {
    f_license_number: val => val.substr(0, 1) + '·' + val.substr(1, val.length - 1)
  },

  methods: {/*绑定的一些方法*/
    //点击 查看违章详情
    ViewDetails: function(code) {
      sessionStorage.is_support_online_pay = 'yes';

      let query = {
        device: this.device,
        openid: this.openid,
        license_number: this.license_number,
        code
      }
      this.$router.push({ name: 'CarViolation__Detail', query })
    },
    //点击 确认支付
    clk_submit: function() {
      let params = {
        source: this.source,
        code: this.code,
        device: this.device,
        openid: this.openid,
        license_number: this.license_number
      }
      this.$http.post('/index.php/Wechat/CarViolation/toPay', params, {
        transformResponse: [result => result]
      })
        .then(res => {
          if (res.code === 0) {
            orderid = res.data.orderid
            return WxPay(res.data.data)
          } else {
            return Promise.reject({ errMsg: res.msg })
          }
        })
        .then(res => {
          console.info('->->Log :: 支付成功的回调返回参数:', res)
          this.$router.replace({
            name: 'CarViolation__PaySuccess',
            query: { orderid }
          })
        })
        .catch(res => { // 支付失败的处理逻辑0
          if (res.err_msg == 'get_brand_wcpay_request:cancel') { // 取消支付

          } else { // 其他失败
            this.$vux.confirm.show({
              title: '提示！',
              content: res.err_desc || res.errMsg,
              cancelText: '取消支付',
              confirmText: '重新支付',
              onCancel: () => console.info('->->Log :: 用户取消支付'),
              onConfirm: () => this.clk_submit()
            })
          }
        })
    }
  },
  created() {
    let params = { license_number: this.license_number, code: this.code }
    this.$http.post('/index.php/Wechat/CarViolation/confirmOrder', params)
      .then(result => {
        if (result.code === 0) {
          this.items = result.data
        }
      })
  }
}
</script>
