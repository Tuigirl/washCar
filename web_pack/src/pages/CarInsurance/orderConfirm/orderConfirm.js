import '@/assets/css/api/carInsurance/orderConfirm.css'
import '@/assets/css/api/carInsurance/InsuranceHeader.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'
// import axios from 'axios';

document.addEventListener("DOMContentLoaded", ()=>{
	new Vue({
		el: '#container',
		mounted() {
			load()
		},
		methods: {
			// 调Native支付Api
			pay(type){
				let _ = this;
				$.post(methods[type], {orderId: orderId}, function (response) {
					if(typeof response === 'string'){
						response = JSON.parse(response)
					}
					if (!_.$jsBridge.isOldVersion) {
						_.$jsBridge.callHandler(
							_.$jsBridgeCmd.invoke
							, {
								method: 'pay',
								params: response
							}
							, (responseData) =>{/*回调函数*/
								responseData = JSON.parse(responseData)
console.warn('responseData', responseData);
								let code = responseData.resultCode
								if(responseData.payCode == 'Wechat'){//微信
									switch (~~code) {
										case 0: paySuccess();break;//支付成功
										case -1: layer.open({content: '微信版本异常', time: 2});break;
										case -2: layer.open({content: '用户中途取消', time: 2});break;
										default: layer.open({content: '支付失败', time: 2})
									}
								}else if(responseData.payCode == 'alipay'){//支付宝
									switch (~~code) {
										case 9000: paySuccess();break;//支付成功
										case 4000: layer.open({content: '支付失败', time: 2});break;
										case 6001: layer.open({content: '用户中途取消', time: 2});break;
										case 6002: layer.open({content: '网络连接失败', time: 2});break;
										default: layer.open({content: '支付失败', time: 2})
									}
								}else {
									layer.open({content: '参数异常' + JSON.stringify(responseData), time: 2})
								}
							}
						)
					} else {//兼容老版本
						for (let key in response.data) {
							if (response.data.hasOwnProperty(key)) {
								response[key] = response.data[key]
							}
						}
						delete response.data
						location.href= 'caryu::type=pay?' + encodeURI(JSON.stringify(response))
					}
				});
				// axios.post(methods[type], {
			  //   orderId: orderId,
			  // })
				// .then(function (response) {
				// 	_.$jsBridge.callHandler(
				// 		_.$jsBridgeCmd.invoke
				// 		, {
				// 				method: 'pay',
				// 				params: response.data
				// 			}
				// 		, (responseData) =>{/*回调函数*/}
				// 	)
			  // })
				// .catch(function (error) {
			  //   console.error(error);
			  // })
			},
		}
	})
}, false);
