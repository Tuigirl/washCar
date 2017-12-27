<template>
  <div id="violationDetail">
    <Scroller ref="myScroller" height="-54" lock-x scrollbar-y>
      <ul id="wrapper" class="con">
        <li>
          <section :style="{background: item.Degree > 0&&'#F8F8F8'}">
            <div class="address">
              <template v-if="item.LocationName && item.LocationName.length > 3">
                {{ '['+item.LocationName+']'+item.Location }}
              </template>
              <template v-else>
                <span class="city" v-bind:style="{left: city_left}">{{ '['+item.LocationName+']' }}</span>
                {{ item.Location }}
              </template>
            </div>
            <div class="time">
              <span class="c-A1">时间：</span>{{ item.Time }}</div>
            <div class="reason">
              <span class="c-A1">原因：</span>
              <span class="reason-s">{{ item.Reason }}</span>
            </div>
            <div class="code">
              <span class="c-A1">代码：</span>{{ item.Code }}
            </div>
            <div class="fine">
              <span class="c-A1">罚款：</span>{{ '¥ '+(item.count||'0.00') }}
              <template v-if="item.Degree > 0">
                <span class="c-A1">扣分：</span>{{ (item.Degree||0)+' 分' }}
              </template>
            </div>
            <div class="hint" v-if="item.Degree > 0">{{ item.CanprocessMsg }}</div>
          </section>
        </li>
      </ul>
    </Scroller>
    <ul class="foot_button" @click="clk_submit" v-show="is_support_online_pay">
      <li role="button">去办理</li>
    </ul>
  </div>
</template>

<script>
import '@/assets/css/api/common.css'
import '@/assets/css/api/CarViolation/violationList.css'
import { Scroller } from 'vux'
export default {

  name: 'CarViolation__List',

  inject: ['isEmptyObject'],

  components: { Scroller },

  data() {
    let { code, openid, device, license_number } = this.$route.query
    return {
      code,
      openid,
      device,
      license_number,
      item: {}
    }
  },

  computed: {
    city_left() {
      if (this.isEmptyObject(this.item)) return '-1em'
      return -(this.item.LocationName.length || 0) - 1 + 'em'
    },
    //判断列表中罚单 是否支持在线交罚款
    is_support_online_pay() {
      return sessionStorage.is_support_online_pay != 'yes' && this.item.Degree == 0
    },
  },

  created() {
    let params = { license_number: this.license_number, code: this.code }
    this.$http.post('/index.php/Wechat/CarViolation/violationDetail', params)
      .then(result => {
        if (result.code === 0) {
          this.item = result.data
        }
      })
  },

  mounted() {
    let promise = new Promise(resolve => setTimeout(() => resolve(), 300))
    promise.then(res => sessionStorage.is_support_online_pay = void 0)
  },

  methods: {
    clk_submit() {
      let query = {
        device: this.device,
        openid: this.openid,
        license_number: this.license_number,
        code: this.code.toString()
      }
      this.$router.push({ name: 'CarViolation__ConfirmPay', query })
    }
  }
}
</script>

