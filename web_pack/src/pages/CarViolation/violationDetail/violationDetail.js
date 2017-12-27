import '@/assets/css/api/CarViolation/violationList.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'
import { GetUrlRequest } from '@/util.js'

var device = GetUrlRequest().device
var code = GetUrlRequest().code
var openid = GetUrlRequest().openid
var license_number = GetUrlRequest().license_number

document.addEventListener("DOMContentLoaded", () => {
	var item = document.querySelector('#data');
	item = JSON.parse(data.innerText);

	var vm = new Vue({
		el: '#all-box',
		data: {
			item: item || new Array(),//包含渲染的数据
			city_left: -(item.LocationName.length || 0) - 1 + 'em'
		},
		computed: {
			//判断列表中罚单 是否支持在线交罚款
			is_support_online_pay() {
				return sessionStorage.is_support_online_pay != 'yes' && item.Degree == 0
			},
		},
		mounted() {
			let promise = new Promise(resolve => setTimeout(() => resolve(), 300))
			promise.then(res => sessionStorage.is_support_online_pay = void 0)
		},
		methods: {/*绑定的一些方法*/
			//点击 去办理
			clk_submit() {
				$.JumpURL('/Xiaochengxu/CarViolation/confirmOrder', {
					device,
					code,
					openid,
					license_number
				})
			}
		}
	});//vm---end

	window.vm = vm;//用于调试
})
