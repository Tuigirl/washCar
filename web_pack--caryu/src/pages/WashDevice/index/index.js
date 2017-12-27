import './index.styl' /* css */
import Vue from '@/pages/main'
import CommonMixin from '@/mixins'

document.addEventListener('DOMContentLoaded', _init)

function _init() {
  let Main = {
    mixins: [CommonMixin],
    data() {
      return {
        dialogVisible: false,
        total: '',                 // 数据总条数
        tableData: [],             // 表格数据
        time_start: '',
        time_end: '',
        ajaxParams: {
          city: 0,
          device: '',
          time_start: '',
          time_end: '',
          page: 1,
          rows: 20,
          currentPage: 1
        },
        editRow: {
          washDevice: '',
          address: '',
          city: ''
        }
      }
    },
    created() {
      this.getDataList()
    },
    watch: {
      ajaxParams: {
        deep: true,
        handler() {
          this.getDataList()
        }
      }
    },
    filters: {
      formatStatus(val) {
        switch (~~val) {
          case 0: return '故障'
          case 1: return '正常'
          default: return '--'
        }
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
      addDeviceHandler(editRow) {
        if (!editRow.city)
          return this.Msg.info('请选择城市！')
        if (!editRow.address)
          return this.Msg.info('请填写地址！')
        if (!editRow.washDevice)
          return this.Msg.info('请填写设备序列号！')
        this.addDevice()
      },
      addDevice() {
        $.post(
          "/Caryu/WashDevice/addDevice"
          , this.editRow
          , res => {
            if (res.code === 0) {
              this.Msg.success(res.msg || '添加成功')
            } else {
              this.Msg.error(res.msg || res.info || '添加失败')
            }
          }, 'json')
      },
      getDataList() {
        let ajaxParams = Object.assign({}, this.ajaxParams)
        if (ajaxParams.time_end == '') ajaxParams.time_start = ''
        $.post(
          "/Caryu/WashDevice/washDeviceList"
          , this.ajaxParams
          , res => {
            this.tableData = res.list || []
            this.total = res.total
          }, 'json')
      }
    }
  }

  let Ctor = Vue.extend(Main)
  window.vm = new Ctor().$mount('#global_layout')
}