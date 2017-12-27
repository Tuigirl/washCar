import '@/assets/css/api/CarViolation/paySuccess.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'
import { GetUrlRequest } from '@/util.js'

document.addEventListener("DOMContentLoaded", () => {
	var item = document.querySelector('#data');
	item = JSON.parse(item.innerText);

	if (item.license_number) {/*车牌号样式处理*/
		item.license_number = item.license_number.substr(0, 1) + '·' + item.license_number.substr(1, item.license_number.length - 1);
	}
	/**
	* 订单列表 加载器
	*/
	var vm = new Vue({
		el: '#all-box',
		data: {
			item: item || new Array()
		},
		methods: {
			// BackHome() {
			// 	if (window.__wxjs_environment === 'miniprogram') { // 小程序里
			// 		wx.miniProgram.navigateTo({ url: `/pages/?` })
			// 	}
			// }
		}
	});//vm---end

	window.vm = vm;//用于调试
}, false)
