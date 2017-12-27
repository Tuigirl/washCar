import './index.styl' /* css */
import Vue from '@/pages/main'
import CommonMixin from '@/mixins'

document.addEventListener('DOMContentLoaded', _init)

function _init() {
  let Main = {
    mixins: [CommonMixin],
    data() {
      return {
        fullscreenLoading: true,
        total: '',                 // 数据总条数
        tableData: [],             // 表格数据
        time_star: '',
        time_end: '',
        ajaxParams: {
          to_excel: 0, // 是否下载Exce
          time_star: '',  // 开始时间
          time_end: '', // 结束时间
          device: '', // 设备名称
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
    methods: {
      tableRowClassName(row) {
        if (row.status == -1) {
          return 'warning-row'
        } else if (row.status == 3) {
          return 'success-row'
        }
        return ''
      },
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
          "/Caryu/WashCar/orderList"
          , ajaxParams
          , res => {
            setTimeout(() => this.fullscreenLoading = false, 800)
            if (res.code == 0) {
              if (this.ajaxParams.to_excel === 1) {
                location.href = res.data.replace('./Public', '/Public')
                this.ajaxParams.to_excel = 0
              } else {
                this.tableData = res.data.list||[]
                this.total = ~~res.data.total
              }
              $('.gl-history-msg').scrollTop(0)
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