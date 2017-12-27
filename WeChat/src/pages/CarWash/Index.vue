<style lang="stylus" scoped>
@import '../../styles/_variables'
.container
  overflow hidden
.below__box
  w = 51px
  height 999px
  position relative
  padding-top 8px
  .unknown .x-cell__left
    color deault-placeholder-color
  .scan-button-1
    width w
    position absolute
    height w
    top -(w/3)
    left 50%
    margin-left -(w/2)
    border-radius 50%
    box-shadow 0 0 0 rgba(0, 0, 0, .35)
  .scan-button-2
    width w = 90px
    height w
    margin 13px auto 16px
  .arrow
    width 8px
    height 13px
    margin-left 5px
  .sum
    color color-yellow
.page__footer
  background-color white
  padding 0 10px 25px
  box-sizing border-box
  button
    border 1px solid lighten(#D49B00, 7%)!important
    font-size 18px!important
    height 46px!important
    line-height 46px!important
    border-radius 5px!important
 .coupon-item
  margin-bottom 20px
</style>
<template lang="pug">
  section(class='container')
    Scroller(ref="myScroller" height="-71" lock-x scrollbar-y)
      XHeader(class='above__box')
      div(class='below__box')
        //- img(class='scan-button-1', src='../../assets/Caryu-sys@2x.png', @click="scanQrcode('yes')")
        XCell(label='洗车费用', :value.sync='_costMoney', :class="{ unknown: unknown }")
        XCell(label='洗车时间', :value.sync='_costTime', :class="{ unknown: unknown }")
        XCell(label='优惠券', :value.sync='_currentRedPacket', :class="{ unknown: unknown }", @tap='showCouponTap')
          img(class='arrow', src="../../assets/Caryu-yhj-gd-icon@2x.png", v-if='!unknown && redPacket.length > 0')
        XCell(class='sum', value='合计金额：')
          PriceSplit(v-model='_sum')
        //- img(
        //-   class='scan-button-2',
        //-   src='../../assets/Caryu-sys-icon@2x.png',
        //-   @click='scanQrcode',
        //-   v-if='unknown')

    div(class='page__footer' v-if='!unknown')
      x-button(text='确定支付', @click.native='confirmPayHandler')

    XPopup(:visible.sync='isShowCoupon', title='选择优惠券', background='#f5f5f5')
      Scroller(slot='content' ref='popupScroller' height='260px' lock-x scrollbar-y)
        section(style='padding-bottom: 1px')
          XCoupon(
            v-for='(item, index) in redPacket', :key='index'
            class='coupon-item',
            :value.sync='item',
            :desc.sync='item.desc',
            :newUser.sync='item.newUser',
            @select='selectorHandler')
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { Scroller, XButton, querystring, numberComma } from 'vux'
import XHeader from '@/components/x-header'
import XPopup from '@/components/x-popup'
import XCoupon from '@/components/x-coupon'
import XCell from '@/components/x-cell'
import { WxPay } from '@/utils'

export default {
  name: 'CarWash__ConfirmPay',

  components: {
    Scroller, XButton,
    XHeader, XPopup, XCoupon, XCell,
    PriceSplit: {
      template: `<div>
                   <span class="pull-left">¥&nbsp;</span>
                   <span class="pull-left" style="font-size: 19px;line-height: 15px">{{ V[0] }}</span>
                   <span class="pull-left">.{{ V[1] }}</span>
                 </div>`,
      props: ['value'],
      data() {
        return { V: this.value.split('.') }
      },
      watch: {
        value(val) {
          this.V = this.value.split('.')
        }
      }
    }
  },

  data() {
    return {
      unknown: false, // 是否待支付状态
      isShowCoupon: false, // 是否显示优惠券弹框
      costMoney: 0, // 洗车费用
      costTime: 0, // 洗车时间(秒)
      currentRedPacket: { // 当前选中的优惠券
        id: '',
        money: 0
      },
      redPacket: [ // 优惠券列表示例数据
        {
          id: '4',
          name: '洗车优惠券',
          type: 'new_washer',
          money: '3.00',
          status: '0',
          remark: '新用户',
          over_time: '2017-12-03 15:08:43',
          add_time: '2017-11-30 15:08:43',
          use_time: '0000-00-00'
        }
      ]
    }
  },

  computed: {
    ...mapState(['openid']),
    ...mapState('CarWashData', ['DeviceSerial']),
    _costMoney() {
      if (this.unknown)
        return '扫码显示'
      return `¥ ${numberComma(this.costMoney)}`
    },
    _costTime() {
      if (this.unknown)
        return '扫码显示'
      return `${Math.round(this.costTime / 60)}分钟`
    },
    _currentRedPacket() {
      const name = this.currentRedPacket.name
      const money = this.currentRedPacket.money
      if (this.unknown)
        return ''
      else if (money === 0)
        return '暂无优惠券可用'
      return name
    },
    _sum() {
      let sum = this.unknown ? 0 : this.costMoney - this.currentRedPacket.money
      return parseFloat(numberComma(sum)).toFixed(2)
    }
  },

  methods: {
    ...mapActions('CarWashData', ['getOrderStatus']),

    showCouponTap() {
      if (!this.unknown && this.redPacket.length > 0)
        this.isShowCoupon = true
    },

    selectorHandler(value, desc) {
      if (value.can_use === -1) {
        this.$vux.alert.show({
          title: '提示！',
          content: '该优惠券不在使用时间范围之内'
        })
      } else if (value.can_use === -2) {
        this.$vux.alert.show({
          title: '提示！',
          content: '优惠券已失效'
        })
      } else {
        this.isShowCoupon = false
        this.currentRedPacket = value
      }
    },

    // scanQrcode(flag) {
    //   if (flag === 'yes' && this.unknown) return false

    //   wepy.scanCode({ onlyFromCamera: true }) // 只允许从相机扫码
    //     .then(res => {
    //       console.log('->->Log :: 扫码成功返回的参数为: ', res)

    //       let params = res.path || res.result
    //       params = params.split('?')[1]
    //       if (!res.path) {
    //         params = decodeURIComponent(params).split('?')[1]
    //         params = params.replace('#wechat-redirect', '')
    //       }
    //       params = queryString.parse(params)
    //       if (params.DeviceSerial) {
    //         this.DeviceSerial = params.DeviceSerial
    //         this.unknown = false
    //         this.getWashCarInfo()
    //       } else {
    //         wepy.showModal({
    //           title: '提示',
    //           showCancel: false,
    //           content: '未获取到设备号, 请重新扫码。'
    //         })
    //       }
    //     })
    //     .catch(err => console.warn('->->Error :: 扫码失败: ', err))
    // },
    confirmPayHandler() {
      this.pay(this.DeviceSerial, this.currentRedPacket.id)
    },

    pay(DeviceSerial, redPacketId) {
      let params = {
        openid: this.openid,
        DeviceSerial,
        redPacketId
      }
      this.$http.post('/index.php/Wechat/WashCar/toPay', params)
        .then(res => { // 获得微信支付的参数,包括签名
          let verify_data = res.data.data
          if (res.code === 0) {
            let orderid = this.$store.state.CarWashData.orderid = res.data.orderid
            return { verify_data, orderid }
          }
        })
        .then(({ verify_data, orderid }) => { // 校验支付金额与后台是否一致
          return this.$http.post(
            '/index.php/Wechat/WashCar/checkOrderStatus',
            { orderid },
            {
              transformResponse: [result => {
                result.verify_data = verify_data
                return result
              }]
            }
          )
        })
        .then(result => { // 调起支付
          if (result.code === 0) {
            // this.jump({ costTime: 5, DeviceSerial: 170005 })
            this.requestPayment(result, DeviceSerial, redPacketId)
          }
        })
        .catch(err => { // 校验支付失败
          console.warn('->->Warn :: 支付参数检验失败:', err)
        })
    },

    jump({ costTime, DeviceSerial }) {
      this.$router.replace({
        name: 'CarWash__Valuation',
        params: { costTime, DeviceSerial }
      })
    },

    requestPayment({ verify_data }, DeviceSerial, redPacketId) {
      return WxPay(verify_data)
        .then(res => {
          console.info('->->Log :: 支付成功的回调返回参数:', res)
          this.jump(this)
        })
        .catch(res => { // 支付失败的处理逻辑
          if (res.err_msg == 'get_brand_wcpay_request:cancel') { // 取消支付

          } else { // 其他失败
            this.$vux.confirm.show({
              title: '提示！',
              content: res.err_desc || res.errMsg,
              cancelText: '取消支付',
              confirmText: '重新支付',
              onCancel: () => console.info('->->Log :: 用户取消支付'),
              onConfirm: () => this.pay(DeviceSerial, redPacketId)
            })
          }
        })
    },

    getWashCarInfo() {
      let params = { openid: this.openid, DeviceSerial: this.DeviceSerial }

      this.$http.post('/index.php/Wechat/WashCar/toWash', params)
        .then(res => {
          let data = res.data
          if (res.code === 0) {
            this.costMoney = data.cost_money
            this.costTime = data.cost_time
            this.redPacket = data.redPacket.map(item => {
              item.newUser = item.type === 'new_washer'
              item.money = parseFloat(item.money)
              item.desc = {
                title: item.name,
                can_use: item.can_use,
                start_time: item.start_time,
                over_time: item.over_time,
                condition: item.remark
              }
              return item
            })

            let redPacket = this.redPacket.filter(item => item.can_use > 0)
            redPacket.forEach(item => {
              if (this.currentRedPacket.money < item.money)
                this.currentRedPacket = item
            })
          }
        })
    },

    init() {
      this.getOrderStatus(this.openid)
        .then(({ status, data }) => {
          status
            ? this.jump({ costTime: data.time_left, DeviceSerial: data.device })
            : this.getWashCarInfo()
        })
    }
  },
  created() {
    this.init()
    // this.unknown = true
  }
}
</script>
