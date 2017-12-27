<style lang="stylus" scoped>
#pullCon {
  padding: 5px 0;
  margin: 0 5px;
  li {
    position: relative;
    width: 100%;
    border: 1px solid #EAEAEA;
    background-color: white;
    font-size: 0.923rem;
  }
  li:not(:first-child) {
    margin-top: 5px;
  }
  .order-head {
    background-color: #F7F7F7;
    padding-left: 1.077rem;
    padding-right: 1.231rem;
    line-height: 3.231rem;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    justify-content: space-between;
    h3 {
      font-size: 1.308rem;
      font-weight: 400;
    }
    span:first-child {
      padding-right: 7px;
      font-size: 0.846rem;
    }
  }
  .order-info {
    & > div:not(:first-child) {
      margin-left: 1.923rem;
      margin-right: 1.692rem;
      line-height: 0.923rem;
      display: -webkit-box;
      display: -webkit-flex;
      display: flex;
      -webkit-box-align: center;
      -webkit-align-items: center;
      align-items: center;
      -webkit-box-pack: justify;
      -webkit-justify-content: space-between;
      justify-content: space-between;
    }
    .fine {
      border-bottom: 1px solid #F0F0F0;
      margin: 0 0.769rem;
      padding: 0 5px 0 1.231rem;
      line-height: 3.769rem;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: space-between;
      & > div span:nth-child(2) {
        padding-left: 2.154rem;
      }
      .pay_btn {
        color: #DE4E4E;
        border: 1px solid #C14343;
        border-radius: 2px;
        line-height: 1.615rem;
        width: 3.077rem;
        float: right;
        text-align: center;
      }
    }
    .toll {
      padding-top: 1.231rem;
    }

    .payment-amount {
      padding-top: 0.692rem;
      padding-bottom: 1.385rem;
    }
  }
  .predict {
    text-align: center;
    margin-top: -5px;
    margin-bottom: 14px;
  }
}
</style>

<template>
  <Scroller ref="myScroller" lock-x scrollbar-y>
    <div id="wrapper" class="con">

      <transition-group tag="ul" id="pullCon" enter-active-class="animated flipInX" @after-enter="$refs.myScroller.reset()">
        <li :key="index" v-for="(item, index) in items" @click="ViewDetails(item)">
          <section class="order-head">
            <h3 class="c-36">
              {{ item.license_number | f_license_number }}
            </h3>
            <div>
              <span v-if="item.orderStatus==0&&item.isDiffShow" class="c-A1">
                剩余{{ Math.floor(item.time_left / 60) }}分{{ item.time_left % 60 }}秒
              </span>
              <span :style="item.orderStatus | order_status('style')">
                {{ item.orderStatus | order_status('text') }}
              </span>
            </div>
          </section>
          <section class="order-info">
            <div class="fine">
              <div>
                <span class="c-A1">违章：</span>{{ item.num }}
                <span class="c-A1">罚款：</span>{{ '¥ '+(item.fine||'0.00') }}
              </div>
              <span class="pay_btn" v-if="item.orderStatus==0" @click.stop="go_wx_pay(item)">
                支付
              </span>
            </div>
            <div class="toll">
              <span class="c-A1">办理服务费：</span>
              <span class="c-59">¥ {{ item.poundage | numberComma }}</span>
            </div>
            <div class="payment-amount">
              <span class="c-A1">支付金额：</span>
              <span class="c-36">¥ {{ item.amount | numberComma }}</span>
            </div>
          </section>
          <section class="predict c-A1" v-if="item.orderStatus==1">
            (预计3~5个工作日完成)
          </section>
        </li>
      </transition-group>

      <NoList :list="items" width="50%" text="您当前没有订单" />

    </div>
  </Scroller>
</template>

<script>
import '@/assets/css/api/common.css'
import { WxPay } from '@/utils'
import { mapState } from 'vuex'
import { Scroller, numberComma } from 'vux'
import { NoList } from '@/components'

export default {
  name: 'CarViolation__Orders',

  inject: ['GetSign'],

  components: { Scroller, NoList },

  data() {
    return {
      count: 0,
      items: []
    }
  },

  computed: {
    ...mapState(['openid']),
    // computedList() {
    //   return this.items.filter(
    //     v => (v.orderStatus == 0 && v.orderStatus != -1)
    //   )
    // }
  },

  filters: {
    f_license_number: val => val.substr(0, 1) + '·' + val.substr(1, val.length - 1),
    order_status(status, type) {
      let obj = {}
      switch (~~status) {
        case 0: obj = { text: '未支付', style: { color: '#DE4E4E' } }; break
        case 1: obj = { text: '办理中', style: { color: '#C2961E' } }; break
        case 2: obj = { text: '已完成', style: { color: '#7CCC2E' } }; break
        case 3: obj = { text: '已退款', style: { color: '#A5A5A5' } }; break
        case -1: obj = { text: '已失效', style: { color: '#A5A5A5' } }; break
        default: obj = { text: '未知状态', style: { color: '#C2961E' } };
      }
      return obj[type]
    },
    numberComma(val) {
      if (~~val === 0)
        return numberComma('0.00')
      return numberComma(val)
    }
  },

  methods: {
    //点击 查看违章详情
    ViewDetails(item) {
      // let params = {
      //   third_order_id: item.third_order_id, //三方的订单
      //   violation_order_id: item.violation_order_id //我们的订单
      // }

      // $.JumpURL('/Api/CarViolation/orderDetail', params);
    },
    go_wx_pay(item) {
      const { source, SecondaryUniqueCodes, violation_order_id, openid, device, license_number } = item
      const code = JSON.parse(SecondaryUniqueCodes).toString()
      // let params = {
      //   source,
      //   code,
      //   device,
      //   openid,
      //   license_number
      // }
      let params = {
        type: 'carViolation',
        orderid: violation_order_id
      }
      this.$http.post('/index.php/Wechat/WxPay/wxPay', params, {
        transformResponse: [result => result]
      })
        .then(res => {
          if (res.code === 0) {
            return WxPay(res.data.data)
          } else {
            return Promise.reject({ errMsg: res.msg })
          }
        })
        .then(res => {
          console.info('->->Log :: 支付成功的回调返回参数:', res)
          this.$router.replace({
            name: 'CarViolation__PaySuccess',
            query: { orderid: violation_order_id }
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
              onConfirm: () => this.go_wx_pay(item)
            })
          }
        })
    },
    //未支付订单的预留时间
    get_surplus_date() {
      this.items.forEach(v => {
        if (v.isDiffShow && v.time_left > 0) {
          let timer = setInterval(() => {
            if (v.time_left <= 0) {//超过一小时的订单失效
              v.isDiffShow = false
              v.orderStatus = -1
              clearInterval(timer)
            } else {
              v.time_left--
            }
          }, 1000)
        }
      })
    },
    getOrderList() {
      this.$store.dispatch('delayExecute', 300) // 拿到openid之后再去拿订单
        .then(result => {
          if (!this.openid) return this.getOrderList()
          let params = {
            openid: this.openid,
            page: 1,
            rows: 100
          }
          params.sign = this.GetSign(params)
          this.$http.post('/index.php/Wechat/CarViolation/wxCarViolationList', params)
            .then(result => {
              if (result.code === 0) {
                result.data.list.forEach(v => { //对 未支付的订单 添加预留时间
                  v.isDiffShow = v.orderStatus == 0
                })
                this.count = result.data.count
                this.items = result.data.list
                this.get_surplus_date()
              }
            })
        })
    }
  },

  created() {
    this.getOrderList()
  }
}
</script>
