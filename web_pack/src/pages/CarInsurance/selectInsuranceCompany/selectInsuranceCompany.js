import '@/assets/css/api/carInsurance/selectInsuranceCompany.css'
import Vue from '@/assets/viewJs/api/js/vue/vue.min.js'

document.addEventListener("DOMContentLoaded", ()=>{
	new Vue({
		el: '.con',
		data: {
      isActiveGoToPage: true
    },
		mounted() {
			init(this)
		},
		methods: {
			// 跳转上传身份证页面Native
			goToPage(order_id, search_type, license_number){
				let data = {order_id: order_id,search_type: search_type,license_number: license_number}
console.warn('带到身份证页面的参数:', data);
				if(!this.$jsBridge.isOldVersion){
					this.$jsBridge.callHandler(
						this.$jsBridgeCmd.invoke
						, {
							method: 'goToPage',
							params: {name: 'IdentifyInsurance', data: data}
						}
						, (responseData) =>{/*回调函数*/}
					)
				}else{//兼容之前版本
					jump(order_id)
				}
			},
		}
	})
}, false);

function init (Vue){
	//如果订单失效 不进行核保和下一步的点击动作
	if($('.company_info[data-type=timeout]').size() > 0){
		Vue.isActiveGoToPage = false;
		$('.company_info[data-type=timeout]').click(function(){/*失效订单的click*/
			layer.open({
				content: '对不起，订单已经失效不能支付。',
				time: 2
			});
		});
		return;
	}

	/*自动核保*/
	~(function () {

	    var order_id = [], verify = [], timer = [], underwri_status = [], status = [];
	    window.highRiskPrompt = '<h4>您要投保的车辆属于高风险车辆</h4>';
	        highRiskPrompt += '1.高风险车辆包括：<br>新车亏损车型、旧车亏损车型<br>2.车辆出险次数高导致投保风险高，只能按保单价投保<br>3.高风险车辆只能按保单价投保<br>4.高风险车辆可能会导致出单失败<br>';

	    /*获取每个订单的order_id、underwri_status、status*/
	    $('.separate').each(function (i) {
	        order_id.push($(this).attr('data-id'));
	        underwri_status.push($(this).attr('data-uwStatus'));
	        status.push($(this).attr('data-status'));
	    });

	    $.each(order_id, function (i, obj) {/*遍历每个订单 得到需要核保的 订单*/

	        var objDom= $('.separate[data-id='+ obj +']').next().find('.HeBaoStatu');

	        /*
	        **type[0:默认，1:用来控制 核保失败 再核一次保，2:人工核保]
	        **timering[人工核保轮询定时器]
	        */
	        timer.push({type: 0, timering: null});

	        if (status[i]==2 && (underwri_status[i]==-1||underwri_status[i]==3)) {/*符合 自动核保*/

	            verify[i] = function () {/*为需要核保的订单 分配 预任务*/

	                var sendData = '/session_id/'+ session_id +'/order_id/'+ obj;
	                $.ajax({/*请求*/
	                    url: methods.underwri +sendData,
	                    type: 'GET',
	                    dataType: 'json',
	                    success: function(data) {

	console.warn('核保信息',data);
	                            if (data.code == 0) {/*核保成功*/

	                                if (data.data.danger_mark == 1) {/*高风险*/
	                                    objDom.html('<div class="iwarnSuccess"></div>核保成功<img src="/Public/static/image/api/w-b.png">').css({background:'#9ACD58'});

	                                    /*高风险车型 的点击提示*/
	                                    objDom.find('.iwarnSuccess').click(function (e) {
	                                        e.stopPropagation();
	                                        explain(highRiskPrompt, true);
	                                    });
	                                    /*折扣价格 与 保单价格需要保持一致*/
	                                    var priceDom1 = objDom.parents('.company_info').find('.normal .total_number');
	                                    var priceDom2 = objDom.parents('.company_info').find('.discount-price .total_number');
	                                    priceDom2.text(priceDom1.text()).next('span').text(priceDom1.next('span').text());

	                                }else {/*正常*/
	                                    objDom.html('核保成功<img src="/Public/static/image/api/w-b.png">').css({background:'#9ACD58 url(/Public/static/image/api/botton-duigou.png)  2.23rem center no-repeat',backgroundSize: '1.308rem'});

	                                }

	                                // 核保成功之后的需要添加跳转下个页面 事件
	                                objDom.parents('.company_info').on('click',function (){
	                                    Vue.goToPage(obj, search_type, license_number);
	                                });

	                                if (timer[i]['timering']) {/*如果为人工核保返回的 结果，存在定时器，需要清除*/
	                                    clearTimeout(timer[i]['timering']);
	                                }

	                            }else if (data.code == 1) {/*核保失败*/

	                                if (timer[i]['type'] == 0) {/*第一次核保 失败之后 再请求一次核保*/

	                                    verify[i]();
	                                }else if (timer[i]['type'] == 1) {/*第二次核保也失败的话 再展示失败信息*/

	                                    /*展示失败的描述信息,data.data.msg为壁虎 返回的描述信息*/
	                                    objDom.text('核保失败：' + data.data.msg).css({background:'#ff5a60 url(/Public/static/image/api/close.png) 2.23rem center no-repeat',backgroundSize: '1rem'});
	                                    /*点击查看'详细'的失败描述信息*/
	                                    objDom.click(function () {
	                                        explain(data.data.msg);
	                                    });
	                                }

	                                if (timer[i]['timering']) {/*如果为人工核保返回的 结果，存在定时器，需要清除*/
	                                    clearTimeout(timer[i]['timering']);
	                                }

	                                timer[i]['type'] = 1;

	                            }else if (data.code == 3) {/*人工核保，需要轮询*/

	                                if (timer[i]['type'] == 0) {/*分配定时器*/
	                                    timer[i]['timering'] = setInterval(function () {
	                                        verify[i]();
	console.info('------------------人工核保轮询中');
	                                    }, 6000);

	                                    objDom.text('核保中：已转入人工核保，请稍候');

	                                }else if (timer[i]['type'] == 2) {

	                                }

	                                timer[i]['type'] = 2;
	                            }

	                    }
	                });//$.ajax 结束

	            };//function verify[i] 结束

	            verify[i]();

	        }
	    });//each结束
	})();

	~(function () {
	    $('.insur_wrap').each(function (o) {/*商业险节点的插入*/

	        var dl = $(this).find('.con_body_item'),
	            biz_num = dl.parents('div.offer_sus').attr('data-bizNum'),/*商业险总数*/
	            bizTotal = dl.parents('div.offer_sus').attr('data-BizTotal'),/*商业险总额*/
	            bizTotal = bizTotal/1 ? bizTotal : '----.--',
	            Color = bizTotal/1 ? '#2d3841' : '#a1a1a1';

	        var ShangYeHTML = '<dl class="con_body_item shangYe" data-flag=1><dt><div class="insurance_name">商业险<span> ×'+ biz_num +'</span></div><span class="price_number"></span></dt><dd><span class="total_number" style="color:'+ Color +'">'+ bizTotal +'</span></dd><img class="gray_arrow" src="/Public/static/image/api/smallf7.png"><img class="biz_arrow" src="/Public/static/image/api/arrow.png"></dl>';/*商业险HTML*/

	        if (biz_num == 0) {
	            return false;
	        }else if (dl.eq(0).attr('data-type') == "JQXCCS") {

	            dl.eq(0).after(ShangYeHTML);
	        }else{
	            $(this).prepend(ShangYeHTML);
	        }

	        if (dl.eq(0).attr('data-type') == "JQXCCS"&&biz_num>0) {
	            $('.shangYe').css({borderTop: '1px dashed #E5E5E5'});
	        }
	    });
	})();

	~(function(){
			//点击出售金额旁边的问号
			$('.doubt').on('click', function(e) {
				e.stopPropagation();
				layer.open({
					content: '出售金额 = 折扣金额 + 返点金额',
					time: 2
				});
			})
			//高风险车型点击提示
			$('.iwarnSuccess').on('click', function(e) {
				e.stopPropagation();
				explain(highRiskPrompt, true)
			})

			$('dl.shangYe').click(function(e) {//商业险展开收缩动画
				e.stopPropagation()
				var type = $(this).parents('.company_info').attr('data-type');

				if ($(this).attr('data-flag')==1) {
					$(this).attr('data-flag',0).parent().find('dl[data-type=biz]').slideDown(300);
					$(this).find('img.gray_arrow').show().next('img.biz_arrow').css({transform: "rotate(90deg)"});
				}else {
					$(this).attr('data-flag',1).parent().find('dl[data-type=biz]').slideUp(300);
					$(this).find('img.gray_arrow').hide().next('img.biz_arrow').css({transform: "rotate(270deg)"});

				}
			})

	    priceRecombine($('.total_number'));/*价格拆分 展示*/
	})();
}
