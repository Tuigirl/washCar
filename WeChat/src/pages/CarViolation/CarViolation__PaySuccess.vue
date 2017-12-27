<style lang="stylus" scoped>
#wrapper {
  li {
    width: auto;
  }
  .paySuccess-head div {
    text-align: center;
    img {
      display: inline;
      width: 7.385rem;
      height: 7.385rem;
    }
    &:nth-child(1) {
      margin-top: 2.615rem;
    }
    &:nth-child(2) {
      line-height: 1.539rem;
      font-size: 1.539rem;
      margin: 1.077rem 0;
    }
    &:nth-child(3) {
      line-height: 0.923rem;
      font-size: 0.923rem;
      margin-bottom: 2.231rem;
    }
  }

  .paySuccess-info {
    margin: 0 5px;
    border: 1px solid #EAEAEA;
  }

  .order-sn {
    height: 2.538rem;
    padding: 0 1.077rem;
    font-size: 0.846rem;
    background-color: #F7F7F7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    div {
      padding: 0;
    }
  }

  .order-info {
    background-color: white;
    .license-number {
      padding-top: 1.308rem;
      padding-bottom: 0.923rem;
      font-size: 1.308rem;
      text-align: center;
      color: #383838;
      position: relative;
      &:after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 50%;
        margin-left: -0.846rem;
        text-align: center;
        display: block;
        width: 1.692rem;
        height: 2px;
        background-color: #E5E5E5;
      }
    }
    .fine-info {
      margin: 4px auto 0 auto;
      padding-bottom: 2.154rem;
      width: 23.769rem;
      border-bottom: 1px solid #F0F0F0;
      div {
        line-height: 1rem;
        padding-top: 1.154rem;
        display: -webkit-box;
        display: -webkit-flex;
        display: flex;
        align-items: center;
        justify-content: space-around;
      }
      span {
        width: 30%;
        white-space: nowrap;
      }
    }
  }

  .payment-amount {
    line-height: 5.215rem;
    text-align: center;
    .c-A1 {
      font-size: 0.846rem;
      vertical-align: super;
    }

    .yellow {
      font-size: 1.846rem;
      vertical-align: sub;
    }
  }


  li[role="button"] {
    width: 8.692rem;
    height: 3.462rem;
    line-height: 3.462rem;
    margin: 1.692rem auto;
    border-radius: 23px;
    color: #694D00;
    text-align: center;
    background: #FFBB00;
  }
}
</style>

<template>
  <div>
    <ul id="wrapper" class="con">
      <li class="paySuccess-head">
        <div><img src="../../assets/img/api/violation/pay_success.png"></div>
        <div class="c-36">支付成功</div>
        <div class="c-A1">您还可以在微信的服务通知中查看此交易详细信息</div>
      </li>
      <li class="paySuccess-info">
        <section class="order-sn">
          <div class="c-A1">
            {{ item.violation_order_id }}
          </div>
          <div class="c-A1">
            {{ item.add_time }}
          </div>
        </section>
        <section class="order-info">
          <div class="license-number">{{ item.license_number }}</div>
          <div class="fine-info">
            <div>
              <span class="c-A1 t-a-r">违章：</span>
              <span class="c-59">{{ item.num }}</span>
            </div>
            <div>
              <span class="c-A1 t-a-r">罚款：</span>
              <span class="c-59">{{ '¥ '+(item.fine||'0.00') }}</span>
            </div>
            <!-- <div>
                  <span class="c-A1 t-a-r">扣分：</span>
                  <span class="c-59">{{ (item.degree||0)+' 分' }}</span>
              </div> -->
            <div>
              <span class="c-A1 t-a-r">服务费：</span>
              <span class="c-59">{{ '¥ '+(item.poundage||'0.00') }}</span>
            </div>
          </div>
          <div class="payment-amount">
            <span class="c-A1">共支付</span>
            <span class="yellow">{{ '¥'+(item.amount||'0.00') }}</span>
          </div>
        </section>
      </li>
      <li role="button" @click="BackHome">完成</li>
    </ul>

  </div>
</template>

<script>
import '@/assets/css/api/common.css'

export default {
  name: 'CarViolation__PaySuccess',

  data() {
    const { orderid } = this.$route.query
    return {
      orderid,
      item: {}
    }
  },

  created() {
    this.$http.post('/index.php/Wechat/CarViolation/paySuccess', { orderid: this.orderid })
      .then(result => {
        if (result.code === 0) {
          this.item = result.data
        }
      })
  },

  methods: {
    BackHome() {
      WeixinJSBridge && WeixinJSBridge.call('closeWindow')
    }
  }
}
</script>
