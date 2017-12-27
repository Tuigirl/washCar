export default {
  data() {
    return {
      city_id: '', // 城市ID
      city_name: '', // 城市
      car_model: '', // 车型
      isShowInsurance: false,
      isShowViolation: false,
      isShowUsed: false,
      isShowLoan: false,
      carInfo: {},
      carInsurance: [],
      carViolation: {},
      carUsed: {},
      carLoan: {}
    }
  },

  methods: {
    goToPage(name) {
      const query = this.WebViewConfig.query
      this.$router.push({ name, query })
    },

    formatTime(t, p) {
      let s = t.split(' ')[0]
      let r = s.split(/-|\//)
      p && (r[1] = (r[1] - 6) < 1 ? (r[0] = r[0] - 1, 12 - (r[1] - 6)) : r[1] - 6)
      return p ? r[0] + '年' + r[1] + '月' : s
    },

    insureAmount(amount) {
      if (!amount) return ''
      if (amount / 10000 >= 1) {
        let i = Math.floor(amount / 10000).toString()
        i = i > 5 && i < 10
          ? '5' : i.length === 2 && i < 100 ? i[0] + '0' : i
        return i + '万'
      }
      return Math.round(amount / 1000) + '千'
    },

    f(s) {
      s = s.split(',')
      s[s.length - 1] = '000'
      return s.join(',')
    }
    // getAddressData() {
    //   return this.$http.post('/index.php/Wechat/Loans/getCityList').then(res => {
    //     if (res.code == 0)
    //       return res.data
    //   })
    // }
  }

}
