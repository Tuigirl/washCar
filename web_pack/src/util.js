import md5 from '~assets/lib/encrypt/md5.min.js'
const __key__ = 'c8428d0773fc7a656b7cfcfb0cb4ac62' //sign验证密钥


/* 内容区高度自适应 */
function reWinSize() {
  var winHeight = $(window).height();
  var headerHeight = $('header').css('display') == 'none' ? 0 : $('header').outerHeight(!0);
  var foot_button = $('.foot_button').length > 0 ? $('.foot_button').outerHeight(!0) : 0;
  var myHeight = winHeight - headerHeight - foot_button;
  $('.con').css('overflow-y', 'auto').outerHeight(myHeight);
}


/**
 *JQ扩展 POST方式跳转URL
 * @param {String} url:要跳转的url
 * @param {Object} args:附加参数
 * @param {String} type:get|post
 */
$.extend({
  JumpURL: function (url, args, type) {

    var form = $("<form method='" + type + "'></form>"),
      input, type = type || 'get'
    form.attr({ "action": url });
    $.each(args, function (key, value) {
      input = $("<input type='hidden'>");
      input.attr({ "name": key });
      input.val(value);
      form.append(input);
    });

    form.appendTo(document.body);
    form.submit();
    document.body.removeChild(form[0]);
  }
});

/**价格拆分重组
 * @param {jQueryObject} $pars 传值jQuery对象
 * 拆分前<span>4999.08</span>
 * 拆分后<span>4999.</span><span>08</span>
 */
function priceRecombine($pars) {
  $pars.each(function () {
    var ob = !$(this).text().trim() ? ['0', '00'] : $(this).text().replace(/\s+/g, '').split('.');
    if (ob.length && ob[0].trim() != '-----' && $(this).next('span').size() == 0) {
      ob[1] = !ob[1] ? '00' : ob[1].length < 2 ? ob[1] + '0' : ob[1];
      // ob[1] += !$(this).parents('.foot_button').size()?'':' &nbsp; 元';
      $(this).text(ob[0]).after('<span>.' + ob[1] + '</span>');
    }
  });
}
/**将CSS属性转为数值
 * @param {jQueryObject} $obj:传值jQuery对象
 * @param {String} attribute:属性名
 */
function size($obj, attribute) {
  return parseInt($obj.css(attribute).replace('px', ''));
}

/**
 * layer弹窗扩展 带确定按钮
 * @param conten{String} content
 */
function confirm_popbox(content) {
  var btnHTML = '<button style="display: block;width: 100%;text-align: center;margin: 0 auto;height: 35px;border-top: 1px solid #e5e5e5;">确定</button>';
  layer.open({ content: content });
  $('.layermmain').css('zIndex', 1000).find('.layermchild').append(btnHTML).find('button').click(function () {
    $('.layermbox').remove();
  });
}

/**
 * JSON对象按照ASCII码排序的函数
 * @param {Object} obj:需要排序的JSON对象
 */
function objKeySort(obj) {
  let newkey = Object.keys(obj).sort();
  //先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
  let newObj = {};//创建一个新的对象，用于存放排好序的键值对
  for (let i = 0; i < newkey.length; i++) {//遍历newkey数组
    newObj[newkey[i]] = obj[newkey[i]];//向新创建的对象中按照排好的顺序依次增加键值对
  }
  return newObj;//返回排好序的新对象
}
/**
 * 生成随机校验串
 * @param {Int} len:生成的随机川长度
 */
function randomString(len = 32) {
  /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  let $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
  let maxPos = $chars.length;
  let pwd = '';
  for (let i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}

/**
 * JSON格式转URL参数格式
 * @param {String} baseUrl   基于此URL拼接(可选)
 * @param {Object} obj       需要转的JSON对象
 */
function JsonToUrlParam(baseUrl, obj) {
  if (typeof baseUrl === 'object') (obj = baseUrl, baseUrl = '')
  else baseUrl += '?'

  for (let k in obj) {
    baseUrl += k + '=' + obj[k] + "&"
  }
  return baseUrl.substr(0, baseUrl.length - 1);
}

/**
 * 签名验证
 * @param {Object} params
 */
function getSign(params) {
  let str = JsonToUrlParam(objKeySort(params)) + '&key=' + __key__;

  let sign_temp = md5(str).toUpperCase(), sign = [];
  for (let i = 0; i < sign_temp.length; i++) {
    if (i % 2 === 0)
      sign.push(sign_temp.substr(i, 2))
  }
  sign.forEach((v, i) => sign[i] += randomString(1))
  return sign.join('');
}

/**
 * 获取url中的参数,返回JSON对象
 */
function GetUrlRequest() {
  var url = decodeURI(location.search);
  var theRequest = new Object();
  if (url.indexOf("?") != -1) {
    var str = url.substr(1), strs = str.split("&");
    for (var i = 0; i < strs.length; i++) {
      theRequest[strs[i].split("=")[0].trim()] = strs[i].split("=")[1].trim()
    }
  }
  return theRequest;
}

/**
 * 正则验证
 * @param {Object} name
 * @param {Object} value
 */
function reg(name, value) {
  switch (name) {
    case 'name': return value.match(/^[\u4e00-\u9fa5_a-zA-Z]{0,10}$/g)
    case 'tel': return value.match(/^(1[0-9])\d{9}$/g)
    case 'price': return value.match(/(^(\d{0,5})\.\d{0,2}$)|(^\d{0,5}$)/g)
    case 'km': return value.match(/(^(\d{0,3})\.\d{0,2}$)|(^\d{0,3}$)/g) && value <= 100
    case 'license_number': return value.match(/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/)
  }
}

module.exports = {
  reWinSize,
  priceRecombine,
  size,
  confirm_popbox,
  JsonToUrlParam,
  getSign,
  GetUrlRequest,
  reg
}
