 var pageJS = {
 	defaultPageSize : 20,
    pageList : [10,20,50,100,200]
 }

var DictTypeJS = {
	JOB_TYPE : "JOB_TYPE",
	EDUCATION : 'EDUCATION'
}

/**
 * 获取url参数列表
 */
function getUrlParms()
{
    var args=new Object();
    var query=location.search.substring(1);//获取查询串
    var param=query.split("&");//在逗号处断开
    for(var i=0;i<param.length;i++)
    {
        var pos=param[i].indexOf('=');//查找name=value
            if(pos==-1)   continue;//如果没有找到就跳过
            var argname=param[i].substring(0,pos);//提取name
            var value=param[i].substring(pos+1);//提取value

            args[argname]=unescape(value);//存为属性
    }
    return args;
}
function isNullValue(str){
	if(str == null || str == "" || str == "undefined"){
		return "";
	}else{
		return str;
	}
}

function isNullDouble(s){
	if(s == null || s == 0){
		return "0.00";
	}else{
		return formatNumber(s,configJS.dot);
	}
}
function isNullRate(s){
	if(s == null || s == "0"){
		return "100";
	}else{
		return s;//formatNumber(s,configJS.dot);
	}
}
function isNull4Double(s){
	if(s == null || s == 0.00){
		return "0.0000";
	}else{
		return formatNumber(s,4);
	}
}


function setDoubleValue(s){
	if(s == null || s== ""){
		return "0.00";
	}else{
		return formatNumber(s,2);
	}
}
function setIntegerValue(s){
	if(s == null || s== ""){
		return "0";
	}else{
		return s;
	}
}
/**
 *  浮点数的格式化，srcStr:要格式化的数，nafterDot:要保留几位小数
 **/
function formatNumber(srcStr,nAfterDot){
	  if(srcStr == null){
	  	 return 0.00;
	  }
      var srctr,nAfterDot;
      var resultStr,nTen;
      srcStr = ""+srcStr+"";
      strLen = srcStr.length;
      dotPos = srcStr.indexOf(".",0);
      if (dotPos == -1){
          resultStr = srcStr+".";
          for (i=0;i<nAfterDot;i++){
              resultStr = resultStr+"0";
          }
          return resultStr;
      } else{
          if ((strLen - dotPos - 1) >= nAfterDot){
              nAfter = dotPos + nAfterDot + 1;
              nTen =1;
              for(j=0;j<nAfterDot;j++){
              nTen = nTen*10;
          }
          resultStr = Math.round(parseFloat(srcStr)*nTen)/nTen;
          return resultStr;
          } else{
              resultStr = srcStr;
              for (i=0;i<(nAfterDot - strLen + dotPos + 1);i++){
                  resultStr = resultStr+"0";
              }
              return resultStr;
          }
      }
  }

    /* 两个日期相减，返回相差分钟数
     * 返回正数时，说明  t1 > t2
     * 返回负数时，说明  t1 < t2
     */
 function minuteDiff(t1, t2){
        tempT1 = null;
        tempT2 = null;
        if (typeof(t1) == "string") //当传入字符串的时间格式时
        {
            t1 = t1.replace(/\-/g, "/");
            tempT1 = new Date(t1);
        }
        else
            if (typeof(t1) == 'object') {
                tempT1 = t1;
            }
        if (typeof(t2) == "string") //当传入字符串的时间格式时
        {
            t2 = t2.replace(/\-/g, "/");
            tempT2 = new Date(t2);
        }
        else
            if (typeof(t2) == 'object') {
                tempT2 = t2;
            }
        return (tempT1 - tempT2) / 1000 / 60; //把相差的毫
}
    /* 两个日期相减，返回相差天数
     * 返回正数时，说明  t1 > t2
     * 返回负数时，说明  t1 < t2
     */
    function dateDiff(t1, t2){
        tempT1 = null;
        tempT2 = null;
        if (typeof(t1) == "string") //当传入字符串的时间格式时
        {
            t1 = t1.replace(/\-/g, "/");
            tempT1 = new Date(t1);
        }
        else
            if (typeof(t1) == 'object') {
                tempT1 = t1;
            }
        if (typeof(t2) == "string") //当传入字符串的时间格式时
        {
            t2 = t2.replace(/\-/g, "/");
            tempT2 = new Date(t2);
        }
        else
            if (typeof(t2) == 'object') {
                tempT2 = t2;
            }
        return (tempT1 - tempT2) / 1000 / 60 / 60 / 24; //把相差的毫
    }

     function monthDiff(t1, t2){
        tempT1 = null;
        tempT2 = null;
        if (typeof(t1) == "string") //当传入字符串的时间格式时
        {
            t1 = t1.replace(/\-/g, "/");
            tempT1 = new Date(t1);
        }
        else
            if (typeof(t1) == 'object') {
                tempT1 = t1;
            }
        if (typeof(t2) == "string") //当传入字符串的时间格式时
        {
            t2 = t2.replace(/\-/g, "/");
            tempT2 = new Date(t2);
        }
        else
            if (typeof(t2) == 'object') {
                tempT2 = t2;
            }
        return (tempT1 - tempT2) / 1000 / 60 / 60 / 24 / 30; //把相差的毫
    }
/**
 * 时间格式转换
 * */
Date.prototype.frameFormatDate = function(formatStr){
	if(formatStr == undefined || formatStr == ""){
		if($("DateFormatStr"))
		{
			formatStr = $("DateFormatStr").value;
		}
		else
		{
			formatStr = DATE_FORMAT;//
		}

	}

	var year = this.getFullYear();
	var month = this.getMonth() + 1;
	month = month<10 ? ("0" + month) : month;
	var date = this.getDate();
	date = date<10 ? ("0" + date) : date;
	var hour = this.getHours();
	hour = hour<10 ? ("0"+hour):hour;
	var minute = this.getMinutes();
	minute = minute<10 ? ("0"+minute):minute;
	var second = this.getSeconds();
	second = second<10 ? ("0"+second):second;
	var str = "";
	switch(formatStr){
		case "yyyy-MM-dd":
			str = year + "-" + month + "-" + date;
			break;
		case "MM/dd/yyyy":
			str = month + "/" + date + "/" + year;
			break;
		case "dd/MM/yyyy":
			str = date + "/" + month + "/" + year;
			break;
	    case "yyyy:MM:dd":
			str = year + "年" + month + "月" + date+"日";
			break;
		case "yyyyMMddhhmmss":
		    str = year+month+date+hour+minute+second;
		    break;
		case "yyyy-MM-dd hh:mm:ss":
		    str = year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
		    break;
		case "yyyy:MM":
		    str = year + "年" + month + "月";
			break;
		default:
			str = year + "-" + month + "-" + date;
			break;
	}
	return str;
}
/**
 * 时间格式转换
 * */
Date.prototype.format = function(formatStr){
	if(formatStr == undefined || formatStr == ""){
		if($("DateFormatStr"))
		{
			formatStr = $("DateFormatStr").value;
		}
		else
		{
			formatStr = DATE_FORMAT;//
		}

	}

	var year = this.getFullYear();
	var month = this.getMonth() + 1;
	month = month<10 ? ("0" + month) : month;
	var date = this.getDate();
	date = date<10 ? ("0" + date) : date;
	var hour = this.getHours();
	hour = hour<10 ? ("0"+hour):hour;
	var minute = this.getMinutes();
	minute = minute<10 ? ("0"+minute):minute;
	var second = this.getSeconds();
	second = second<10 ? ("0"+second):second;

	var timeValue = "";
	if(hour >12){
	   timeValue = "下午 "+parseInt(hour) - 12+":"+minute;
	}else{
	   timeValue = "上午 "+hour+":"+minute;
	}
	var str = "";
	switch(formatStr){
		case "yyyy-MM-dd":
			str = year + "-" + month + "-" + date;
			break;
		case "MM/dd/yyyy":
			str = month + "/" + date + "/" + year;
			break;
		case "dd/MM/yyyy":
			str = date + "/" + month + "/" + year;
			break;
	    case "yyyy:MM:dd":
			str = year + "年" + month + "月" + date+"日";
			break;
		case "yyyyMMddhhmmss":
		    str = year+month+date+hour+minute+second;
		    break;
		case "yyyy:MM":
		    str = year + "年" + month + "月";
			break;
		case "hh:mm":
		    str = timeValue;
			break;
		default:
			str = year + "-" + month + "-" + date;
			break;
	}
	return str;
}

//日期格式的处理
Date.prototype.formatOther = function(format)
{
   var o = {
     "M+" : this.getMonth()+1, //month
     "d+" : this.getDate(),    //day
     "h+" : this.getHours(),   //hour
     "m+" : this.getMinutes(), //minute
     "s+" : this.getSeconds(), //second
     "q+" : Math.floor((this.getMonth()+3)/3), //quarter
     "S" : this.getMilliseconds() //millisecond
   }
   if(/(y+)/.test(format)) format=format.replace(RegExp.$1,
     (this.getFullYear()+"").substr(4 - RegExp.$1.length));
   for(var k in o)if(new RegExp("("+ k +")").test(format))
     format = format.replace(RegExp.$1,
       RegExp.$1.length==1 ? o[k] :
         ("00"+ o[k]).substr((""+ o[k]).length));
   return format;
}

/**
 * 时间格式转换
 * */
Date.prototype.formatOnline = function(formatStr){
	if(formatStr == undefined || formatStr == ""){
		if($("DateFormatStr"))
		{
			formatStr = $("DateFormatStr").value;
		}
		else
		{
			formatStr = DATE_FORMAT;//
		}

	}

	var year = this.getFullYear();
	var month = this.getMonth() + 1;
	month = month<10 ? ("0" + month) : month;
	var date = this.getDate();
	date = date<10 ? ("0" + date) : date;
	var hour = this.getHours();
	hour = hour<10 ? ("0"+hour):hour;
	var minute = this.getMinutes();
	minute = minute<10 ? ("0"+minute):minute;
	var second = this.getSeconds();
	second = second<10 ? ("0"+second):second;
	var timeValue = "";
	if(hour >12){
	   timeValue = "下午 "+parseInt(hour) - 12+":"+minute;
	}else{
	   timeValue = "上午 "+hour+":"+minute;
	}
	var str = "";
	//alert(year+"-"+month+"-"+date+" "+hour+":"+month+":"+second);
	switch(formatStr){
		case "yyyy-MM-dd":
			str = year + "-" + month + "-" + date;
			break;
		case "MM/dd/yyyy":
			str = month + "/" + date + "/" + year;
			break;
		case "dd/MM/yyyy":
			str = date + "/" + month + "/" + year;
			break;
	    case "yyyy:MM:dd":
			str = year + "年" + month + "月" + date+"日";
			break;
		case "yyyyMMddhhmmss":
		    str = year+""+month+""+date+""+hour+""+minute+""+second;
		    str = str.Trim();
		    break;
		case "hh:mm":
		    str = hour+":"+minute;
			break;
		case "yyyy:MM":
		    str = year + "年" + month + "月";
			break;
		default:
			str = year + "-" + month + "-" + date;
			break;
	}
	return str;
}
function isNullOtherValue(str){
	if(str == null || str == ""){
		return "";
	}else{
		return str;
	}
}
function isNullUpdateValue(str){
	if(str == null || str == ""){
		return "";
	}else{
		return str;
	}
}
function isNullInteger(s){
	if(s == null || s == ""){
		return 0;
	}else{
		return s;
	}
}
function isNullFloat(s){
	if(s == null){
		return 0.00;
	}else{
		return formatNumber(s,2);
	}
}
function isFormatText(s){
	if(s == null || s == ""){
		return "0";
	}else{
	    if(parseInt(s) < 10){
	       return "0"+s;
	    }else{
			return s;
		}
	}
}
// 去除空格
String.prototype.Trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

  /**
   * 只能办入数字
   * */
  function checkNum(e) {
			if(window.event){
				 if((event.keyCode>=48&&event.keyCode<=57)||event.keyCode==8||(event.keyCode>=96&&event.keyCode<=105) ||event.keyCode==46||event.keyCode==37||event.keyCode==39||event.keyCode==190){
                     event.returnValue=true;
                 }else{
				     event.returnValue=false;
				 }
			}
			else if(e.which){
				if((e.which>=48&&e.which<=57)||e.which==8||(e.which>=96&&e.which<=105)||e.which==46||e.which==37||e.which==39||e.which==190){

                }else{
					e.preventDefault() ;
				}
	         }
  }
    /* 将字符串转化为时间
     * 输入:dateString格式 "2008-10-10 10:10:10" 或 "2008/10/10 10:10:10"
     * 输出:时间
     * */
    function parseDate(mydate){
        var currentDate = null;
        //当没有传入参数时
        if (mydate == undefined) {
            currentDate = new Date();
        }

        //当传入字符串的时间格式时
        else
            if (typeof(mydate) == "string") {
                mydate = mydate.replace(/\-/g, "/");
                currentDate = new Date(mydate);
                if (isNaN(currentDate)) {
                    pisaPop.info(lanObj.common_msg_error,"\n\n传入日期有误 \n\nfile:com.js ;function:parseDate");
                    return;
                }
            }

            //当传入的为日期时
            else {
                currentDate = mydate;
            }
        return currentDate;
    }
    /* 返回当前时间加上几天后的时间
     * 如'2008-12-31' + 2 = '2009-1-2'
     */
    function addDays(mydate, days){

        var currentDate = parseDate(mydate);
        var newDate = new Date(Date.parse(currentDate) + 86400000 * days);
        return {
            "date": newDate,
            "dateString": newDate.getFullYear() + "-" + (newDate.getMonth() + 1) + "-" + newDate.getDate()
        }
    }

  function addDays2(mydate, days){

        var currentDate = parseDate(mydate);
        var newDate = new Date(Date.parse(currentDate) + 86400000 * days);
        var month = newDate.getMonth() + 1;
        if(month < 10){
           month = "0"+month;
        }
        var date = newDate.getDate();
        if(date < 10){
           date = "0"+date;
        }
        return {
            "date": newDate,
            "dateString": newDate.getFullYear() + "-" + month + "-" + date
        }
    }
Array.prototype.remove=function(dx)
{
    if(isNaN(dx)||dx>this.length){return false;}
    for(var i=0,n=0;i<this.length;i++)
    {
        if(this[i]!=this[dx])
        {
            this[n++]=this[i];
        }
    }
    this.length-=1;
};

Array.prototype.removeOther=function(dx)
{
    if(isNaN(dx)||dx>this.length){return false;}
    for(var i=0,n=0;i<this.length;i++)
    {
        if(this[i]!=this[dx])
        {
            this[n++]=this[i];
        }
    }
    this.length-=1;
};
Array.prototype.indexOf=function(value)
{
    for(var i=0;i<this.length;i++)
    {
        if(this[i]==value)
        {
            return i;
        }
    }
    return -1;
};


/**
 * 判断两个时间这间间隔几分钟
 * */
function getMinuteInDates(date1,date2){
	//默认格式为"yyyyMMddhhmmss",根据自己需要改格式和方法
	var year1 =  date1.substr(0,4);
	var year2 =  date2.substr(0,4);

	var month1 = date1.substr(4,2);
	var month2 = date2.substr(4,2);

	var day1 = date1.substr(6,2);
	var day2 = date2.substr(6,2);

    var hour1 = date1.substr(8,2);
	var hour2 = date2.substr(8,2);
	var minute1 = date1.substr(10,2);
	var minute2 = date2.substr(10,2);
	var s1 = date1.substr(12,2);
	var s2 = date2.substr(12,2);
	temp1 = year1+"/"+month1+"/"+day1+" "+hour1+":"+minute1+":"+s1;;
	temp2 = year2+"/"+month2+"/"+day2+" "+hour2+":"+minute2+":"+s2;
	var dateaa= new Date(temp1);
	var datebb = new Date(temp2);

	var date = datebb.getTime() - dateaa.getTime();

	var time = Math.floor(date / (1000 * 60   ));
    return time;
}

function addMonth(date,count){
     var tempT1 = null;
     if (typeof(date) == "string")
     {
            date = date.replace(/\-/g, "/");
            tempT1 = new Date(date);
      }
      var month = tempT1.getMonth() + 1
      var year = tempT1.getFullYear();
      var tempMonth = count+month;
      var result = "";
	  if(parseInt(tempMonth) > 12){
	       tempMonth = tempMonth-12;
	       year = year + 1;
	  }
	  if(tempMonth < 10){
	     result = year+"-0"+tempMonth;
	  }else{
	  	 result = year+"-"+tempMonth;
	  }
	  return result;
}

getwinHeight = function()
{
  var  winH;
  if(window.innerHeight) {
    winH = parent.window.innerHeight;
  } else if (document.documentElement
    && document.documentElement.clientHeight) {
    winH = parent.document.documentElement.clientHeight;
  } else if (document.body) { // other
    winH = parent.document.body.clientHeight;
  }
  return winH;
}
function getScrollTop()
{
    var scrollTop=0;
    if(document.documentElement&&document.documentElement.scrollTop)
    {
        scrollTop=document.documentElement.scrollTop;
    }
    else if(document.body)
    {
        scrollTop=document.body.scrollTop;
    }
    return scrollTop;
}


CookieUtil = {
	EMAIL_COOKIE_NAME:"HISTORY_EMAILS",
	saveEmailAddressInCookie : function(emails) {
		//如果cookie功能关闭则不进行操作
		if(document.cookie == "") return;
		if(emails==null||emails=="") return;
		 var regex=new RegExp("^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$");
		//取得旧的email地址
		var cookie = (unescape(document.cookie)).match(new RegExp("(^| )"+this.EMAIL_COOKIE_NAME+"=([^;]*)(;|$)"));
		var oldEmails;
		if (cookie != null) {
			oldEmailsStr = unescape(cookie[2]);
			var oldEmails = oldEmailsStr.split(',');
		} else {
			oldEmails = new Array();
		}
		//添加新email地址，只有在旧的email中不存在的情况下才添加新的email
		var newEamils = emails.split(',');
		for (var i = 0; i < newEamils.length; i++) {
			var isExist = false;
			var newEmail = newEamils[i];
			if(!regex.test(newEamils[i])||newEmail=="") continue;
			for (var j = 0; j < oldEmails.length; j++) {
				if ( newEmail == oldEmails[j]) {
					isExist = true;
					break;
				}
			}
			if (!isExist) {
				oldEmails.push(newEamils[i]);
			}
		}
		var cookieStr = this.EMAIL_COOKIE_NAME+"=";
		var length = oldEmails.length;
		for (var i = 0; i < length; i++) {
			cookieStr += oldEmails[i];
			if (i != (length - 1)) {
				cookieStr += ',';
			}
		}
		var now = new Date();
		//设置有效期 一个月
		var validate = new Date(now.getTime()+31*24*60*60*1000);
		var cookieDate = validate.toGMTString();
		document.cookie = escape(cookieStr)+";expires=" + cookieDate;
	},
	getCookieEmails:function(){
		//如果cookie功能关闭则不进行操作
		if(document.cookie == "") return new Array();
		var cookie = unescape(document.cookie);
		var cookieArr = cookie.match(new RegExp("(^| )"+this.EMAIL_COOKIE_NAME+"=([^;]*)(;|$)"));
		if(cookieArr!=null){
			var cookieStr = unescape(cookieArr[2]);
			return cookieStr.split(',');
		}else{
			return new Array();
		}
	}
};
var rightCodeJS = {
    PROVIDER_CONFIG_RIGHT  : 101,
	MATVEHICLE_CONFIG_RIGHT  : 102,
	MATTYPE_CONFIG_RIGHT  : 103,
	BASEMAT_CONFIG_RIGHT  : 104,
	MACINFO_CONFIG_RIGHT  : 105,
	OTHERSTK_CONFIG_RIGHT  : 106,
	MATRATE_CONFIG_RIGHT  : 107,
	MACSTK_CONFIG_RIGHT  : 108,

	STRENGTHGRADE_CONFIG_RIGHT  : 201,
	CMTTYPE_CONFIG_RIGHT  : 202,
	CUBERATE_CONFIG_RIGHT  : 203,
	ADDITIVE_CONFIG_RIGHT  : 204,
	WATERING_CONFIG_RIGHT  : 205,
	INTERMAT_CONFIG_RIGHT  : 206,
	FALLRATE_CONFIG_RIGHT  : 207,
	SEEPGRADE_CONFIG_RIGHT  : 208,
	FREEZEGRADE_CONFIG_RIGHT  : 209,
	BREAKGRADE_CONFIG_RIGHT  : 210,
	VEHICLEOWNER_CONFIG_RIGHT  : 211,
	VEHICLE_CONFIG_RIGHT  : 212,
	DRIVER_CONFIG_RIGHT  : 213,
	OUTUNIT_CONFIG_RIGHT  : 214,
	OTHERMAT_CONFIG_RIGHT  : 215,
	VEHICLE2_CONFIG_RIGHT  : 216,

	BASEDEPART_CONFIG_RIGHT : 301,
	GROUP_CONFIG_RIGHT : 302,
	OPERATOR_CONFIG_RIGHT : 303,
	RIGHT_CONFIG_RIGHT : 304,
	OPERATOR_PSW_RIGHT : 305,
	SYSCORPORATION_CONFIG_RIGHT : 306,
	HEADSHIP_CONFIG_RIGHT : 307,
	DICT_CONFIG_RIGHT : 308,
	DICTSTATIC_CONFIG_RIGHT : 309,

	SETTLE_RIGHT : 401,
	WEIGHT_IN_RIGHT : 402,
	OUTWEIGHT_RIGHT : 403,
	OUTWEIGHT_DETAIL_RIGHT : 404,
	WEIGHT_IN_DETAIL_RIGHT : 405,
	SETTLE_DETAIL_RIGHT : 406,
	MAC_ORDER_RIGHT : 407,

	SALEORDER_RIGHT  : 501,
	CONSIGNUNIT_CONFIG_RIGHT  : 502,
	PROJECTINFO_CONFIG_RIGHT  : 503,
	PROJECTPLACE_CONFIG_RIGHT  : 504,
	PROJECTPART_CONFIG_RIGHT  : 505,
	WATERPROC_CONFIG_RIGHT  : 506,
	SALEORDER_LIST_RIGHT : 507,
	PRJCLOSE_RIGHT : 508,

	MAKETASK_RIGHT  : 601,
	VEHICLESCHEDULING_RIGHT  : 602,
	RTN_RIGHT  : 603,
	SENDDOC_RIGHT : 604,
	CHK_RIGHT : 605,

	SALE_SETTLE_RIGHT : 606,
	SALE_SETTLE_DETAIL_RIGHT : 607,

	TASKRECIPE_RIGHT : 701,
	RECIPE_RIGHT : 702,
    TESTAPPLY_RIGHT : 703,
	APPRAISE_RIGHT  : 704,
	CMTTEST_RIGHT : 705,
    ADDITIVETEST_RIGHT : 706,
    BASEMATTEST_RIGHT : 707,
	SANDTEST_RIGHT  : 708,
	STONETEST_RIGHT  : 709,
    MIXAPPLY_RIGHT : 710,

	MAKEDAYREPORT_RIGHT : 801,
	SALEREPORT_RIGHT  : 802,
	OUTVEHICLEREPORT_RIGHT  : 803,
	OUTVEHICLE2REPORT_RIGHT : 804,
	OUTVEHICLEDISREPORT_RIGHT : 805,
	PRJCLOSESTAT_RIGHT : 806,
	PRJCLOSETOTAL_RIGHT : 807,

	PROJECTSTOREREPORT_RIGHT  : 901,
	PROJECTSTOREINDETAIL_RIGHT  : 902,
	MATTYPESTOREREPORT_RIGHT  : 903,
	MATTYPESTOREINDETAIL_RIGHT  : 904,
	MATSTORE_RIGHT  : 905,
	SALEORDERTOTAL_RIGHT  : 906,
	SALEORDERDETAIL_RIGHT  : 907,
	PROJECTSALETOTAL_RIGHT  : 908,
	TYPESALETOTAL_RIGHT  : 909,
	TYPESALEDETAIL_RIGHT  : 910,
	MACINTRANS_RIGHT : 911,

	STKPURMAST_RIGHT : 1101,
	STKDELIVERYMAST_RIGHT : 1102,
	STKRTNMAST_RIGHT : 1103,
	OTHERRECEIVE_RIGHT : 1104,
	OTHERRTN_RIGHT : 1105,
	OTHERTRANSPRICE_RIGHT : 1106,
	OTHERTRANS_RIGHT : 1107,
	TRANSEMP_RIGHT : 1201,
	OILWEAR_RIGHT : 1301,
	OILWEARSTAT_RIGHT : 1302,
	MANAGEQUERY_RIGHT : 1401,
	OPERATORLOGQUERY_RIGHT : 1501

	//end
}
var saleSets = {
	cardinput : "cardinput",
	cardsale : "cardsale",
	saletypevalue : "saletypevalue",
	birtyday : "birtyday",
	cardvalue: "cardvalue",
	cardcost : "cardcost",
	cardcostreturn : "cardcostreturn",
	cardcostrenew : "cardcostrenew",
	cardothershopinput : "cardothershopinput",
	cardothershopsale : "cardothershopsale",
	cardothershopfree : "cardothershopfree",
	ticketpaydiscount : "ticketpaydiscount",
	cardptydiscount : "cardptydiscount",
	ticketvalue : "ticketvalue",
	ticketcheckcode : "ticketcheckcode",
	salecount : "salecount",
	ratetype : "ratetype",
	ticketcodeflag : "ticketcodeflag",
	ticketcode : "ticketcode",
	ticketbatch : "ticketbatch",
	ticketbill : "ticketbill",
	saledaytimebegin : "saledaytimebegin",
	ticketpayout : "ticketpayout",
	forcardsaletotal : "forcardsaletotal",
	ticketsalecheckcode : "ticketsalecheckcode"
}
function getCreateRight(rightCode){
	dwr.engine.setAsync(false);
	var right = 0;
	rightAction.getRightOther(rightCode,"insert",function(flag){
	    right = flag;
	});
	return right;
	dwr.engine.setAsync(true);
}
function getUpdateRight(rightCode){
	dwr.engine.setAsync(false);
	var right = 0;
	rightAction.getRightOther(rightCode,"edit",function(flag){
	    right = flag;
	});
	return right;
	dwr.engine.setAsync(true);
}
function noRightReturn(){
	alert("您没有权限");
    window.open("default.jsp","_self");
    return;
}
var dictBO = {
    PROVIDER_TYPE : "PROVIDER_TYPE",
    MACSTK_TYPE : "MACSTK_TYPE",
    PAY_TYPE : "PAY_TYPE",
    CASH_SOURCE : "CASH_SOURCE",
    TICKET_TYPE : "TICKET_TYPE",
    STKRTNMAST_TYPE : "STKRTNMAST_TYPE"
}
var configJS = {
    webRoot : "/CSCMis/",
    dot : 2,
    cardPaySet : 0,  //卡支付设置
    ticketPaySet : 0, //券支付设置
    sys_index : "-",//系统数值符号

    initBeginQueryDate : function(){
    	var date = new Date();
    	var result = "";
    	var year = date.getFullYear();
		var month = date.getMonth() + 1;
		month = month<10 ? ("0" + month) : month;
		var day = "01";
		//day = day<10 ? ("0" + day) : day;
		result = year+"-"+month+"-"+day;
		return result;
    },


    initEndQueryDate : function(){
    	return new Date().format("yyyy-MM-dd");
    },

    initBeginMonthDate : function(){
    	var date = new Date();
    	var result = "";
    	var year = date.getFullYear();
		var month = date.getMonth() + 1;
		month = month<10 ? ("0" + month) : month;
		var day = "01";
		//day = day<10 ? ("0" + day) : day;
		result = year+"-"+month+"-"+day;
		return result;
    },

    initEndMonthDate : function(){
    	var date = new Date();
    	var result = "";
    	var year = date.getFullYear();
		var month = date.getMonth() + 1;
		month = month<10 ? ("0" + month) : month;
		var day = "01";
		//day = day<10 ? ("0" + day) : day;
		result = year+"-"+month+"-"+day;
		return result;
    }

}

var printDataJS = {
	 Preview : function(title) {
		LODOP.PRINT_INIT("");
		LODOP.ADD_PRINT_HTM(10,300,144,35,"<span style='font-size: 18px;font-weight: bold;'>"+title+"</span>");
		LODOP.ADD_PRINT_HTM(40,10,800,35,"<hr />");

		LODOP.ADD_PRINT_TABLE(100,20,800,950,document.getElementById('printDiv').innerHTML);
		LODOP.PREVIEW();
	}
}

var cookieJS = {
    setCookie : function(cookie_name,value)　　{　　
		　var date = new Date()　　
		　date.setTime(date.getTime() + 1000*3600000 ) //小时　　
		　document.cookie = cookie_name+"='"+value+"';expires="+ date.toGMTString()　　
	},　
    getCookie : function(cookie_name)　　{　　
		　var cookieString = new String(document.cookie)　　
		　var cookieHeader = cookie_name+"="　　
		　var beginPosition = cookieString.indexOf(cookieHeader)　
		  var result = null;　
		　if (beginPosition != -1) //cookie已经设置值，应该 不显示提示框　　
		　{　　
		    result = cookieString.substring(beginPosition + cookieHeader.length);
		　}　　
		　else　//cookie没有设置值，应该显示提示框　　
		　{　　
		　   result = null;　　
		　}　　
		  return result;
	},　　
	 removeCookie : function(Cookie_name)　　{　　
	　     document.cookie = cookie_name+"=;expires=Fri, 02-Jan-1970 00:00:00 GMT";　　
	 }　　

}

 function getElementsByClassName(className) {
	   var all = document.all ? document.all : document.getElementsByTagName('*');
	   var elements = new Array();
	   for (var e = 0; e < all.length; e++) {
	     if (all[e].className == className) {
	       elements[elements.length] = all[e];
	      }
	    }
	    return elements;
 }

 /**
 * 根据类型名称找实体，并将这些实体disable掉
 * */
function disableAllElements(){
	 var className = "saleClassName";
	 var objs = getElementsByClassName(className);
     if(objs != null && objs.length > 0){
     	for(var i=0;i<objs.length;i++){
     		objs[i].disabled = true;
     	}
     }

     var objs = getElementsByClassName("pBtn");
     if(objs != null && objs.length > 0){
     	for(var i=0;i<objs.length;i++){
     		objs[i].disabled = true;
     	}
     }
}
function ableAllElements(){
	 var className = "saleClassName";
	 var objs = getElementsByClassName(className);
     if(objs != null && objs.length > 0){
     	for(var i=0;i<objs.length;i++){
     		objs[i].disabled = false;
     	}
     }

     var objs = getElementsByClassName("pBtn");
     if(objs != null && objs.length > 0){
     	for(var i=0;i<objs.length;i++){
     		objs[i].disabled = false;
     	}
     }
}

//发送查询网银支付情况 author:zhangxr
function doBatchSearchFromBank(){
    alert("2");
    var extraIds = getSelectedRow();
    alert(extraIds);
	/*document.getElementById("processId").style.display = 'block';
	var listId = getRowID();
	var activeRow = window.frames["body_frame"].activeRow;
	if(activeRow.payoutMethod=="1"){
		alert("只有电子支付才能查询该支付的网银结果");
		document.getElementById("processId").style.display = 'none';
		return false;
	}*/
	var url="<c:url value = '/fp/paybank/transcation.do?action=getBatchNetBankInfo&extraIds='/>" + extraIds;
	document.forms[0].action = url;
	document.frmSearch.submit();
}

//取得选择的记录
function getSelectedRow1(){
	var result = "";
	var boxs = body_frame.document.getElementsByName("selects");
    var count=0;
    var extraIds = "";
    var objRow;
    for (i=0;i<boxs.length;i++ ) {
        if ( boxs[i].checked ){
        	count++;
        	if(count==1){
        		extraIds = boxs[i].value;
        	}else{
        		extraIds += ","+boxs[i].value;
        	}
        	objRow = boxs[i].parentNode.parentNode;
        }
    }
    if(count==0){
    	alert("请选择您要查询交易结果的记录！")
    }
    result = extraIds;
    return result;
}

function closeMask(){

}

var tableJS = {
    	 lastSelectTr : null,
    	 setClickColor : function(obj){
    	 	var tds = obj.getElementsByTagName("td");
    	 	if(tds != null){
    	 		for(var i=0;i<tds.length;i++){
    	 			tds[i].style.backgroundColor = "#eafcd5";
    	 		}
    	 	}
    	 	if(this.lastSelectTr != null){
    	 		if(obj.rowIndex != this.lastSelectTr.rowIndex){
	    	 		var lastTds = this.lastSelectTr.getElementsByTagName("td");
		    	 	if(lastTds != null){
		    	 		for(var i=0;i<lastTds.length;i++){
		    	 			lastTds[i].style.backgroundColor = "";
		    	 		}
		    	 	}
    	 		}
    	 	}
    	 	this.lastSelectTr = obj;
    	 }
}
var operatorTypeJS = {
	  LOG_CARD_INPUT_TYPE : '1',
	  LOG_CARD_SHOPIN_TYPE: '2',
	  LOG_CUSTOMER_DELETE_TYPE : '3'
}

function JsonToStr(o) {
	var arr = [];
	var fmt = function(s) {
	if (typeof s == 'object' && s != null) return JsonToStr(s);
		return /^(string|number)$/.test(typeof s) ? "'" + s + "'" : s;
	}
	for (var i in o) arr.push("'" + i + "':" + fmt(o[i]));
		return '{' + arr.join(',') + '}';
}

/**
 * 获得当前月有几天
 */
function daysInMonth(monthDate){
	  /*var t = "2011-02-01";
	  t2 = t.replace(/\-/g, "/");
	var d = new Date(t2);*/
	var curMonthDays = new Date(monthDate.getFullYear(), (monthDate.getMonth()+1), 0).getDate();
	return curMonthDays;
}

var rightInitJS = {
    initModuleId : 1101,
    initGroupId : 1,
    initUserId : 1
}



var  highlightcolor='#edf4f9';
//此处clickcolor只能用win系统颜色代码才能成功,如果用#xxxxxx的代码就不行,还没搞清楚为什么:(
var  clickcolor='#51b2f6';
var source;
function  changeto(){
	source=event.srcElement;
	if(source.tagName=="TR"||source.tagName=="TABLE")
	return;
	while(source.tagName!="TD")
	source=source.parentElement;
	source=source.parentElement;
	cs  =  source.children;
	//alert(cs.length);
	if  (cs[1].style.backgroundColor!=highlightcolor&&source.id!="nc"&&cs[1].style.backgroundColor!=clickcolor)
	for(i=0;i<cs.length;i++){
		cs[i].style.backgroundColor=highlightcolor;
	}
}

function  changeback(){
if  (event.fromElement.contains(event.toElement)||source.contains(event.toElement)||source.id=="nc")
return
if  (event.toElement!=source&&cs[1].style.backgroundColor!=clickcolor)
//source.style.backgroundColor=originalcolor
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor="";
}
}

function  clickto(){
source=event.srcElement;
if  (source.tagName=="TR"||source.tagName=="TABLE")
return;
while(source.tagName!="TD")
source=source.parentElement;
source=source.parentElement;
cs  =  source.children;
//alert(cs.length);
if  (cs[1].style.backgroundColor!=clickcolor&&source.id!="nc")
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor=clickcolor;
}
else
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor="";
}
}


function operateCheckAll(){
        var operateBox = document.getElementsByName("operateBox");
        var checkboxAll = document.getElementById("checkboxAll");
        for(var i=0;i<operateBox.length;i++){
           operateBox[i].checked = checkboxAll.checked;
        }
}

var desktopCode = {
    DESKTOP5 : '1001',
    DESKTOP1 : '1002',
    DESKTOP2 : '1003',
    DESKTOP3 : '1004',
    DESKTOP4 : '1005',
    DESKTOP8 : '1006'
}

/**
 * 保存操作统一调用的公用方法
 */
var comOperateJS = {
	ticketNumLimit : 1000,
	/**
	 * 保存参数设置信息 1：保存成功，0：保存失败
	 */
	updateSetForValue : function(arr) {
		var result = 0;
		var saleConfJson = [];
        saleConfJson.push("[");
        if(arr != null){
	        for(var i = 0;i<arr.length;i++){
	           var obj = JsonToStr(arr[i]);
	           saleConfJson.push(obj);
	           if(i < arr.length - 1){
	           	  saleConfJson.push(",");
	           }
	        }
        }
        saleConfJson.push("]");
		$.post("../../s/sale_conf/addSaleConf", {
			saleConfListStr :saleConfJson.join("")
		}, function(data) {
			if ($.checkErrorMsg(data) ) {
			    if(data.resultCode == 1){
					result = 1;
				}else{
					result = 0;
				}
			}
		}, "json");

		return result;

	},
	/**
	 * 根据操作类型，加载相应的配置信息
	 */
		loadSets : function(code) {
				var saleSetObj = null;
				$.post(
						"../../s/sale_conf/findSaleConf",
						{},
						function(data) {
							if ($.checkErrorMsg(data)) {
								var list = data.saleConfList;
								for ( var i = 0; i < list.length; i++) {
									if (list[i].code == code) {
										 saleSetObj = list[i];
									}
								}

							}
						}, "json");
			   return saleSetObj;
	   }
}

function formatInt(str){
	var result = "";
	if(str.Trim() != ""){
		for(var i = 0;i<str.length;i++){
			var temp = str.substring(i,i+1);
			if(temp != "0"){
				result = str.substring(i,str.length);
				break;
			}
		}
		return result;
	}else{
		return "0";
	}
}

var ticketStatus = {
	 //0：正常  1：已发行2：已配送  3：已销售 4：已回收 5：已作废
	 STATUS_COMMON : 0,//正常
	 STATUS_APPLY : 1,//已发行
	 STATUS_SEND : 2,//已配送
	 STATUS_SALE : 3, //已销售
	 STATUS_BACK : 4, //已回收
	 STATUS_CANCEL : 5 //已作废
}