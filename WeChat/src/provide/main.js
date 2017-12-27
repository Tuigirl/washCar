import Vue from 'vue'
import { GetSign } from '@/utils'

export default {
  GetSign,
  isEmptyObject(obj) {
    for (var key in obj)
      return false
    return true
  },
  ToastFail(text = "返回错误", w = 11) {
    Vue.$vux.toast.show({
      text,
      type: 'text',
      isShowMask: true,
      position: 'middle',
      width: w + 'em'
    })
  }
}
