/**
 * 微信支付
 * @param {Object} params
 * @param {Function} call:回调函数
 */
export default (params) => {
  function onBridgeReady() {
    return new Promise((resolve, reject) => {
      if (typeof params.timeStamp !== 'string')
        params.timeStamp = String(params.timeStamp)
      WeixinJSBridge.invoke(
        'getBrandWCPayRequest', params,
        (res) => {
          if (res.err_msg == "get_brand_wcpay_request:ok") {
            resolve(res)
          } // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
          else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
            console.info('->->Log :: 用户取消支付')
            reject(res)
          } else { // 返回错误
            console.warn('->->Warn :: 支付失败:', res)
            reject(res)
          }
        }
      );
    })
  }
  if (typeof WeixinJSBridge == "undefined") {
    if (document.addEventListener) {
      document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
      document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
      document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
    return Promise.reject({ err_desc: '请在微信内部浏览器使用！' })
  } else {
    return onBridgeReady();
  }
}