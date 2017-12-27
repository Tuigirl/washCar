<style lang="stylus" scoped>

</style>

<template lang="pug">
  section
    Scroller(ref="myScroller" height="-54" lock-x scrollbar-y)
      transition-group(
        v-show="!!items.length"
        tag="ul" id="wrapper" class="con"
        appear
        appear-active-class="animated flipInX")
          li(v-for="(item,index) in items"
            :key="index"
            @click="ViewDetails(item)")
              div(class="cleared" v-if="isCleared(item)")
                img(src="../../assets/img/api/violation/YBL_icon.png")
              section(:style="{background: item.Degree > 0&&'#F8F8F8'}")
                div(class="address") {{ '['+item.LocationName+']' }} {{ item.Location }}
                div(class="time")
                  span(class="c-A1") 时间：
                  | {{ item.Time }}
                div(class="reason")
                  span(class="select" @click.stop="clk_select(index)" :class="{ no_click: item.Degree > 0||isCleared(item) }")
                    img(src="../../assets/img/api/violation/select_no.png" v-show="item.is_select")
                    img(src="../../assets/img/api/violation/select_yes.png" v-show="!item.is_select")
                  div
                    span(class="c-A1") 原因：
                    span(class="reason-s") {{ item.Reason }}
                  span(class="arrow")
                div(class="fine")
                  span(class="c-A1") 罚款：
                  | {{ '¥ '+(item.count||'0.00') }}
                  template(v-if="item.Degree > 0")
                    span(class="c-A1") 扣分：
                    | {{ (item.Degree||0)+' 分' }}
                div(class="hint" v-if="item.Degree > 0") {{ item.CanprocessMsg }}

    ul(class="foot_button" v-show="!!items.length")
        li(class="select_all" @click="clk_selectAll")
          span(v-show="!is_selectAll")
            img(src="../../assets/img/api/violation/selectAll_no.png")
          span(v-show="is_selectAll")
            img(src="../../assets/img/api/violation/selectAll_yes.png")
          span 全选
        li 违章
          animated-integer(v-bind:value="payment_info[0]")
        li 罚款
          animated-integer(v-bind:value="payment_info[1]")
        //- <li>扣分 <animated-integer v-bind:value="payment_info[2]"></animated-integer></li>
        li(role="button" @click="clk_submit") 罚款代缴

    //- 查询 无违章的展示
    ul(id="NoList" v-show="!items.length")
      li
        img(src="../../assets/img/api/violation/no_violation.png")
      li 该车辆暂无违章记录
</template>

<script>
import '@/assets/css/api/common.css'
import '@/assets/css/api/CarViolation/violationList.css'
import { AnimatedInteger } from '@/components'
import { Scroller } from 'vux'
import { mapActions } from 'vuex'

export default {
  name: 'CarViolation__List',

  inject: ['GetSign'],

  components: { AnimatedInteger, Scroller },

  data() {
    let { openid, device, license_number } = this.$route.query
    return {
      openid,
      device,
      license_number,
      items: [],//包含渲染的数据
      is_selectAll: false,//是否全选
      code: new Array(),//违章ID
      payment_info: [0, 0, 0],//[违章个数,罚款金额,总扣分]底部交款信息
      // Locationid: [5101, 4401, 4201],//[四川,广东,武汉] 支持扣分情况下,在线交罚款的城市
    }
  },
  methods: {/*绑定的一些方法*/
    ...mapActions(['getCarViolationList']),
    //是否办理中
    isCleared(item) {
      // 1 正在办理 2 办理完成
      return item.remark_status == 1 || item.remark_status == 2 || item.remark == '正在办理'
    },
    //点击 查看违章详情
    ViewDetails(item) {
      let query = {
        device: this.device,
        openid: this.openid,
        license_number: this.license_number,
        code: item.SecondaryUniqueCode
      }

      if (this.isCleared(item)) {
        sessionStorage.is_support_online_pay = 'yes'
      }

      this.$router.push({ name: 'CarViolation__Detail', query })
    },
    //单选
    clk_select(index) {
      let current = this.items[index], Degree = current.Degree;

      if (this.is_support_online_pay(Degree)) {

        current.is_select = current.is_select ? false : true;//单选切换

        let _ = this;
        this.is_selectAll = this.items//全选状态切换
          .filter(function(v) { return _.is_support_online_pay(v.Degree) })
          .every(function(v) { return v.is_select == false });
      }

      this.set_payment_info();
    },
    //全选
    clk_selectAll() {
      this.is_selectAll = this.is_selectAll ? false : true;//全选切换

      let _ = this;
      this.items.forEach(function(v) {//单选切换
        v.is_select = _.is_selectAll &&
          !_.isCleared(v) &&
          _.is_support_online_pay(v.Degree)
          ? false : true
      });

      this.set_payment_info();
    },
    //判断列表中罚单 是否支持在线交罚款
    is_support_online_pay(Degree) {
      return Degree == 0
    },
    //改变 缴款总额及总扣分
    set_payment_info() {
      let array = [0, 0, 0], code = new Array();

      this.items.filter(function(v) {
        return v.is_select == false;
      }).forEach(function(v, i, arr) {
        array = [
          arr.length,//[违章]
          array[1] + parseFloat(v.count),//[罚款]
          array[2] + parseFloat(v.Degree)//[扣分]
        ],
          code.push(v.SecondaryUniqueCode);//缴款ID
      })

      this.code = code;
      this.$set(this.payment_info, 0, array[0]);
      this.$set(this.payment_info, 1, array[1]);
      this.$set(this.payment_info, 2, array[2]);
    },
    //点击 罚款代缴
    clk_submit() {
      if (this.items.some(v => v.is_select == false)) {
        let query = {
          device: this.device,
          openid: this.openid,
          license_number: this.license_number,
          code: this.code.toString()
        }
        this.$router.push({ name: 'CarViolation__ConfirmPay', query })
      } else {
        this.$vux.toast.show({
          text: '请选择违章',
          type: 'text',
          isShowMask: true,
          position: 'middle',
          width: '9em'
        })
      }
    },
    init() {
      let list = this.$route.params.ViolationList
      if (list && list.length > 0) {
        list.forEach(item => item.is_select = true)
        this.items = list
      } else {
        let params = {
          unlimit: 1,
          openid: this.openid,
          license_number: this.license_number
        }
        this.getCarViolationList(params)
          .then(({ code, data }) => {
            if (code === 0) {
              data.forEach(item => item.is_select = true)
              this.items = data
            }
          })
      }
    }
  },
  created() {
    delete sessionStorage.is_support_online_pay
    this.init()
  }
}
</script>
