import '@/assets/css/api/carInsurance/paySuccess.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'

document.addEventListener("DOMContentLoaded", ()=>{
	new Vue({
		el: '#foot_end',
		methods: {
			// 调Native方法关闭当前WebView
			BackAppRoot(){
				if (!this.$jsBridge.isOldVersion) {
					this.$jsBridge.callHandler(
						this.$jsBridgeCmd.invoke
						, {
							method: 'goToPage',
							params: {
								name: 'AppHome',
								data: ''
							}
						}
					)
				}else {//兼容老版本
					location.href = 'caryu::type=BackRoot';
				}
			},
		}
	})
}, false)
