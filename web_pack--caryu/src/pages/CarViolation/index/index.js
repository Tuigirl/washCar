import Vue from '@/pages/main'
import './index.styl'
import CommonMixin from '@/mixins'

document.addEventListener('DOMContentLoaded', _init)

const statusOptions = [
  { value: 4, label: '全部' },
  { value: 0, label: '已提交' },
  { value: 1, label: '办理中' },
  { value: 2, label: '已完成' },
  { value: -1, label: '已失效' },
  { value: 3, label: '已退款' }
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
          orderStatus: 4,
          license_number: '',
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
        if (!status)
          return ''
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
        this.$http.post("/Caryu/CarViolation/getList", ajaxParams)
          .then(res => {
            setTimeout(() => this.fullscreenLoading = false, 800)
            this.tableData = res.rows || []
            this.total = res.total
          })
      }
    }
  }

  let Ctor = Vue.extend(Main)
  window.vm = new Ctor().$mount('#global_layout')
}