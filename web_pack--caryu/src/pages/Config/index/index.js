import Vue from '@/pages/main'
import './index.styl'

document.addEventListener('DOMContentLoaded', _init)

function _init() {
  let Main = {
    data() {
      return {
        fullscreenLoading: true,
        dialogFormVisible: false,
        total: '',                 // 数据总条数
        tableData: [],             // 表格数据
        currentRow: {},
        method: 'addConfig',
        editRow: {
          key: '',
          value: '',
          explain: ''
        },
        ajaxParams: {
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
      handleSizeChange(rows) {
        this.ajaxParams.rows = rows
      },
      handleCurrentChange(page) {
        this.ajaxParams.currentPage
          = this.ajaxParams.page
          = page
      },
      handleCurrentChange(newRow, oldRow) {
        this.currentRow = newRow
      },
      saveConfig(editRow) {
        if (!editRow.explain)
          return this.Msg.info('请输入权限名称！')
        if (!editRow.key)
          return this.Msg.info('请输入Key！')
        if (!editRow.value)
          return this.Msg.info('请输入Value！')
        this.$http.post(
          `/Caryu/Config/${this.method}`
          , editRow)
          .then(res => {
            setTimeout(() => this.fullscreenLoading = false, 800)
            if (res.code === 0) {
              this.Msg.success(res.msg || '保存成功！')
              this.dialogFormVisible = false
              this.getOrderList()
            }
          })
      },
      editConfig() {
        if ($.isEmptyObject(this.currentRow))
          return this.Msg.info('请选择一条配置')
        this.method = 'updateConfig'
        this.editRow = JSON.parse(JSON.stringify(this.currentRow))
        this.dialogFormVisible = true
      },
      getOrderList() {
        this.$http.post(
          "/Caryu/Config/getConfigList"
          , this.ajaxParams)
          .then(res => {
            setTimeout(() => this.fullscreenLoading = false, 800)
            if (res.code === 0) {
              res.data.list.forEach(item => {
                if (typeof item.value === 'string')
                  try {
                    item.value = JSON.stringify(JSON.parse(item.value))
                  } catch (error) {
                    console.info(error)
                  }
              })
              this.tableData = res.data.list || []
              this.total = res.data.total
            }
          })
      }
    }
  }

  let Ctor = Vue.extend(Main)
  window.vm = new Ctor().$mount('#global_layout')
}