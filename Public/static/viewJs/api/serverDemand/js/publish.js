  $(function(){

		var opt={};
		var currYear = (new Date()).getFullYear();	
		var currMonth = (new Date()).getMonth();	
		var currDate = (new Date()).getDate();	
		var currHours = (new Date()).getHours();	
		var currMins = (new Date()).getMinutes();	
	
		opt.datetime = { preset : 'date', minDate: new Date(currYear-30,currMonth,currDate), maxDate: new Date(currYear,currMonth,currDate)  };
		opt.default = {
			
			theme: 'android-ics light', //皮肤样式
	        mode: 'scroller', //日期选择模式
			dateFormat: 'yyyy-mm-dd',
			lang: 'zh',
			display:'bottom',
			//showNow:true,
			//nowText: "今天",
	        //startYear: currYear - 30, //开始年份
	        //endYear: currYear, //结束年份
	        dayText: '日', monthText: '月', yearText: '年', //面板中年月日文字
	        height:45,
	        width:107,
	        rows: 3,
	        showLabel: false,
	       
	        onBeforeShow:function(){
//	        	$('.con input').blur();
	        },
		};
	  	$("#reach_time").mobiscroll($.extend(opt['datetime'], opt['default']));
//	  	$("#reach_time").mobiscroll("show");
	  	$("#reach_time").unbind('focus');
		$("#reach_time").focus(function(){
			setTimeout(function(){
				  $("#reach_time").mobiscroll("show");
			},350);
		 })

   })
