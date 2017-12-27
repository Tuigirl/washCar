import '@/assets/css/api/CarViolation/violationList.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'
import TWEEN from '@/assets/lib/tween.js'
import { GetUrlRequest } from '@/util.js'

var device = GetUrlRequest().device;
var openid = GetUrlRequest().openid;
var license_number = GetUrlRequest().license_number;

document.addEventListener("DOMContentLoaded", () => {
	delete sessionStorage.is_support_online_pay;

	var items = {}
	var data = document.querySelector('#data');
	var illegal_info = JSON.parse(data.innerText);

	items.illegal_info = illegal_info
	items.license_number = license_number

	if (illegal_info && illegal_info.length && license_number) {/*车牌号样式处理*/
		items.license_number = items.license_number.substr(0, 1) + '·' + items.license_number.substr(1, items.license_number.length - 1);

		if (items.illegal_info) {
			items.illegal_info.forEach(function (v) {
				v.is_select = true;//单选开关
			})
		}
	} else {
		items.license_number = '车牌号为null';
	}

	//Vue组件 数值的补间动画
	Vue.component('animated-integer', {
		template: '<span>{{ tweeningValue }}</span>',
		props: {
			value: {
				type: Number,
				required: true
			}
		},
		data: function () {
			return {
				tweeningValue: 0
			}
		},
		watch: {
			value: function (newValue, oldValue) {
				this.tween(oldValue, newValue)
			}
		},
		mounted: function () {
			this.tween(0, this.value)
		},
		methods: {
			tween: function (startValue, endValue) {
				var vm = this
				function animate(time) {
					requestAnimationFrame(animate)
					TWEEN.update(time)
				}
				new TWEEN.Tween({ tweeningValue: startValue })
					.to({ tweeningValue: endValue }, 450)
					.onUpdate(function () {
						vm.tweeningValue = this.tweeningValue.toFixed(0)
					})
					.start()
				animate()
			}
		}
	});

	/**
	 * 违章 列表加载器
	 */
0	var vm = new Vue({
		el: '#all-box',
		data: {
			items: items || new Array(),//包含渲染的数据
			is_selectAll: false,//是否全选
			code: new Array(),//违章ID
			payment_info: [0, 0, 0],//[违章个数,罚款金额,总扣分]底部交款信息
			// Locationid: [5101, 4401, 4201],//[四川,广东,武汉] 支持扣分情况下,在线交罚款的城市
		},
		methods: {/*绑定的一些方法*/
			//是否办理中
			isCleared(item) {
				return item.remark_status == 1 || item.remark == '正在办理'
			},
			//点击 查看违章详情
			ViewDetails(item) {
				let params = {
					device,
					openid,
					license_number,
					code: item.SecondaryUniqueCode
				}

				if (this.isCleared(item)) {
					sessionStorage.is_support_online_pay = 'yes'
				}

				$.JumpURL('/Xiaochengxu/CarViolation/violationDetail', params)
			},
			//单选
			clk_select(index) {
				var current = items.illegal_info[index], Degree = current.Degree;

				if (this.is_support_online_pay(Degree)) {

					current.is_select = current.is_select ? false : true;//单选切换

					var _ = this;
					this.is_selectAll = items.illegal_info//全选状态切换
						.filter(function (v) { return _.is_support_online_pay(v.Degree) })
						.every(function (v) { return v.is_select == false });
				}

				this.set_payment_info();
			},
			//全选
			clk_selectAll() {
				this.is_selectAll = this.is_selectAll ? false : true;//全选切换

				var _ = this;
				items.illegal_info.forEach(function (v) {//单选切换
					v.is_select = _.is_selectAll &&
						!_.isCleared(v) &&
						_.is_support_online_pay(v.Degree)
						? false : true
				});

				this.set_payment_info();
			},
			//判断列表中罚单 是否支持在线交罚款
			is_support_online_pay(Degree) {
				return Degree == 0
			},
			//改变 缴款总额及总扣分
			set_payment_info() {
				var array = [0, 0, 0], code = new Array();

				items.illegal_info.filter(function (v) {
					return v.is_select == false;
				}).forEach(function (v, i, arr) {
					array = [
						arr.length,//[违章]
						array[1] + parseFloat(v.count),//[罚款]
						array[2] + parseFloat(v.Degree)//[扣分]
					],
						code.push(v.SecondaryUniqueCode);//缴款ID
				})

				this.code = code;
				this.$set(this.payment_info, 0, array[0]);
				this.$set(this.payment_info, 1, array[1]);
				this.$set(this.payment_info, 2, array[2]);
			},
			//点击 罚款代缴
			clk_submit() {
				var _ = this;
				if (items.illegal_info.some(v => v.is_select == false)) {
					$.JumpURL('/Xiaochengxu/CarViolation/confirmOrder', {
						device,
						openid,
						license_number,
						code: _.code.join()
					})
				} else {
					layer.open({ content: '请选择违章', time: 2 });
				}
			}
		}
	});//vm---end

	window.vm = vm;//用于调试
})
