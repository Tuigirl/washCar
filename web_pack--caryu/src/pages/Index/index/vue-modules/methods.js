export default {
  toString: s => String(s),
  showWelcome: () => $('#welcome').parent().css('visibility', 'visible'),
  hideWelcome: () => $('#welcome').parent().css('visibility', 'hidden'),
  f_addTab: (tabid, title, url) => {
    window.f_addTab(tabid, title, url)
    if (tabid === 'Facility-AlarmList')
      window.vm.V_backlogContent['pendingMsg'].child[0].count = 0
  },
  accountSelectHandle(eventType) {
    eventType === 'logout'
      ? loginout()
        : this.dialogFormVisible = true
  },
  accountMenuToggle(isHide) {
    this.isRotate = isHide
  },
  menuItemClick(el) {
    if (el.index == 1000) { // 首页
      this.f_addTab('home', '首页')
    } else {
      let arr = el.index.split('-')
      let active_child = this.menuItems[arr[0]].child[arr[1]]

      this.isLoaded = true
      this.f_addTab(
        active_child.method
        , active_child.title
        , '../Caryu/' + active_child.name
      )
    }
  },
  menuToggle() {
    const  _ = this
    _.isActive = !_.isActive;
    setTimeout(function() {
        $('.el-submenu__title .el-submenu__icon-arrow').css(
            'visibility', _.isActive ? 'hidden' : ''
        )
        _.isCollapse = _.isActive;
    }, 0)
  },
  dialogCloseHandle() {
    this.form.old_pswd = ''
    this.form.new_pswd = ''
  },
  changePswdOk() {
    this._HTTP_().change_pswd($.trim($('#user_box').text()))
  }
}