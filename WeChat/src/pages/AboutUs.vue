<template>
  <scroller ref="myScroller" lock-x scrollbar-y>
    <div id="about_us">
      <section class="page__head">
        <img class="banner" src="../assets/aboutus/banner@2x.png">
      </section>

      <section class="page__body">
        <article class="about">
          <h4>
            <span class="title">关于驾遇快洗</span>
          </h4>
          <p>
            驾遇快洗自助洗车是驾遇互联（驾遇成立于2014年8月，是国内首家B2B2C的车后市场一站式服务平台）旗下子品牌，涉及软硬件研发，制造及运营管理等环节，它利用移动互联网技术"云服务"，解决"云共享"问题，颠覆了传统洗车运营和服务模式，整合洗车行业和汽车后市场的相关业务，引领共享模式下的节能环保洗车服务新潮流。
          </p>
        </article>
        <article class="advantage">
          <h4>
            <span class="title">驾遇快洗优势</span>
          </h4>
          <p>
            省心：24小时自助；<br> 环保：节能、时尚、无污染；
            <br> 省钱：经济实惠、支付便捷；
            <br> 灵活：洗车不设限、扫码可支付；
            <br> 省时：云定位、随到随洗，一枪切换；
            <br> 共享：自助洗车设备共享，与场地合作方共同收益；
          </p>
        </article>
        <article>

        </article>
      </section>

      <footer class="page__footer">
        <a href="tel:01059564072">加盟热线：010-59564072</a>
      </footer>
    </div>
  </scroller>
</template>

<script>
import { Scroller } from 'vux'
import { mapState } from 'vuex'

export default {
  name: 'AboutUs',

  computed: { ...mapState(['QUERY']) },

  components: { Scroller },

  created() {
    if (!this.QUERY.code)
      document.title = '驾遇快洗'
  }
  // this.$nextTick(() => this.$refs.myScroller.reset())
}
</script>

<style lang="stylus" scoped>
text-color = #E56600
[id^=vux-scroller]
  background: white url(../assets/aboutus/xcgywm-bj@2x.png) no-repeat center bottom
  background-size 100% 77%

#about_us
  min-height 705px
  .page__body
    margin 0 22px
    h4
      margin-top 5px
      font-size 15px
      line-height 15px
      padding-top 13px
      padding-bottom 8px
      color text-color
      text-align center
      background-size 80% 100%
      .title
        position relative
        w = 60px
        &::before,
        &::after
          content ''
          display block
          width w
          height (w/12)px
          top 7px
          position absolute
          background url(../assets/aboutus/bq-ys@2x.png) no-repeat center center
          background-size 100%
        size = -(w + 10)px
        &::before
          left size
        &::after
          right size
          transform rotateZ(180deg)
    p
      font-size 13px
    article
      line-height 25px
    .about
      text-align justify
    .advantage
      text-align center
  .page__footer
    margin 35px auto 20px
    a
      font-size 12px
      color text-color
</style>
