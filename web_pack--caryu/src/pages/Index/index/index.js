import Vue from '@/pages/main'
import methods from './vue-modules/methods'
import './index.styl'

document.addEventListener('DOMContentLoaded', _init)

function _init() {
  let Main = {
    data() {
      let menuItems = JSON.parse(document.getElementById('menu-data').innerText)
      return {
        fullscreenLoading: false,
        isCollapse: false,
        dialogFormVisible: false,
        isRotate: false,
        form: {
          old_pswd: '',
          new_pswd: ''
        },
        isActive: false,
        menuItems
      }
    },
    methods,
    watch: {
      isActive(bool) {
        var unfold_left = window.leftWidth, shrink_left = 40, delay = 300
        if (bool) {
          $('.l-layout-left').animate({ width: shrink_left }, delay, 'swing')
            .next().animate(
            { left: shrink_left, width: $(window).width() - shrink_left }
            , delay
            , () => {
              /*侧边栏收缩属性冲突, 补丁*/
              setTimeout(() => {
                $('.l-layout-left').css('zIndex', '12')
                $('#gl-left-nav').css('overflow', 'visible')
              }, delay)
              child_iframe_resize('max')

              /*侧边栏收缩状态小屏会显示不全 ,补丁*/
              !$('style[css-patch]').size() && $('body').append('<style css-patch>ul[el-menu-hover-lt768]{top: auto!important;bottom: 0}</style>')
              if (window.innerHeight <= 768) {
                setTimeout(() => $('.el-submenu .el-menu').attr('el-menu-hover-lt768', ''), 100)
              } else {
                $('.el-submenu .el-menu').removeAttr('el-menu-hover-lt768')
              }
            }
            )
        } else {
          $('.l-layout-left').animate({ width: unfold_left }, delay, 'swing')
            .next().animate(
            { left: unfold_left, width: $(window).width() - unfold_left }
            , delay
            , () => {
              $('.l-layout-left').css('zIndex', '')
              $('#gl-left-nav').css('overflow', '')
              child_iframe_resize('min')
            }
            )
        }

        function child_iframe_resize(ty) {// 除首页之外的所有子iframe页面 窗口大小适配计算
          let temp = []
          $('iframe').each((i, el) => {
            temp.push(el.contentWindow.innerWidth)
          })
          let width = Math[ty].apply(null, temp)
          $('iframe').each((i, el) => {
            el.contentWindow.$('#data_list').datagrid('resize', { width })
          })
        }
      }
    }
  }

  let Ctor = Vue.extend(Main)
  window.vm = new Ctor().$mount('#global_layout')
}