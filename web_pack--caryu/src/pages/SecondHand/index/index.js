import './index.styl' /* css */
import Vue from '@/pages/main'
import CommonMixin from '@/mixins'

document.addEventListener('DOMContentLoaded', _init)

const statusOptions = [
  { value: 0, label: '全部' },
  { value: 1, label: '已提交' },
  { value: 2, label: '处理中' },
  { value: 3, label: '交易成功' },
  { value: 4, label: '交易失败' }
]

function _init() {
  let Main = {
    mixins: [CommonMixin],
    data() {
      return {
        fullscreenLoading: true,
        statusOptions,
        total: '',                 // 数据总条数
        tableData: [],             // 表格数据
        time_star: '',
        time_end: '',
        ajaxParams: {
          city_id: 0,
          status: 0,
          mobile: '',
          time_star: '',      // 开始时间
          time_end: '',       // 结束时间
          page: 1,
          rows: 20,
          currentPage: 1
        }
      }
    },
    created() {
      this.getOrderList()
    },
    watch: {
      ajaxParams: {
        deep: true,
        handler() {
          this.getOrderList()
        }
      }
    },
    filters: {
      formatStatus(status) {
        if (!status || status === '0')
          return '--'
        let temp = statusOptions.filter(item => item.value == status)
        return temp[0].label
      }
    },
    methods: {
      handleSizeChange(rows) {
        this.ajaxParams.rows = rows
      },
      handleCurrentChange(page) {
        this.ajaxParams.currentPage
          = this.ajaxParams.page
          = page
      },
      getOrderList() {
        let ajaxParams = Object.assign({}, this.ajaxParams)
        if (ajaxParams.time_end == '') ajaxParams.time_star = ''
        $.post(
          "/Caryu/SecondHand/secondHandList"
          , ajaxParams
          , res => {
            setTimeout(() => this.fullscreenLoading = false, 800)
            if (!res.list) {
              this.tableData = []
              this.total = 0
            } else if (res.list && res.list.length > 0) {
              this.tableData = res.list
              this.total = ~~res.total
            } else {
              this.Msg.error(res.msg || res.info || '获取数据失败！')
            }
          }, 'json')
      }
    }
  }

  let Ctor = Vue.extend(Main)
  window.vm = new Ctor().$mount('#global_layout')
}