import FastClick from 'fastclick'
import util from '@/util.js'

document.addEventListener("DOMContentLoaded", () => {
	FastClick.attach(document.body) //消除移动端click延迟
	setTimeout(() => util.reWinSize(), 0)
})


//暴露到全局对象下 老版本兼容
window.priceRecombine = util.priceRecombine;
window.size = util.size;
window.confirm_popbox = util.confirm_popbox;
window.eyeToggle = util.eyeToggle;
window.isWechat = navigator.userAgent.match(/\b(MQQBrowser|MicroMessenger)/) || location.search.match(/(debug\=1|open_id\=)/);
// window.fmoney = util.fmoney;
