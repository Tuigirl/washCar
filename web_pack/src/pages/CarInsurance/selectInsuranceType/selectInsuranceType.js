import '@/assets/css/api/carInsurance/chooseInsuranceType.css'
import { GetUrlRequest, reg, JsonToUrlParam } from '@/util.js'

$(function () {
  //页面适配初始化逻辑
  var imgH = $('.price img').height(), marginT = (63 - imgH) / 2;
  $('.price img').css('margin-top', marginT);
  var imgH1 = $('.freeIcon').height(), marginT1 = (63 - imgH1) / 2;
  $('.freeIcon').css('margin-top', marginT1);
  var left = $('.lala').parents().innerWidth() / 2 - 15;
  // console.log($('.lala').parents().innerWidth());
  $('.lala').css('right', left + 'px');
  $('.lala').hide();

  //点击遮罩层 收起弹窗
  $('#mask').click(function () {
    $('.choose_item').animate({ bottom: "-750px" }, 150);
    $(this).fadeOut('fast');
    $('.con').css({ overflowY: 'auto' });//IOS会滚动
  })

  //自动选中上次投保，
  if (last_renewal != '') {
    $('.insur' + last_renewal).addClass('last-time-state');
  }
  //上次选过保险公司

  $.each(company, function (k, r) {
    /* 厦门跳过 太平洋, 唐山跳过 人保, 石家庄跳过 人保, 北京跳过 平安 */
    if (((~~openCity == 15) && (~~r == 1)) || ((~~openCity == 24) && (~~r == 4)) || ((~~openCity == 12) && (~~r == 4)) || ((~~openCity == 1) && (~~r == 2))) {
      return true;
    }
    changeCompany(r);
  });
  //返回自动展开下拉项
  if (tys == 1) {
    showSome(true);
  }
  //自动选中车险细项
  $.each(insuranceArr, function (index, data) {
    //过滤无关项
    var contain = false;
    $.each(_plan, function (key, row) {
      if (index == key) {
        contain = true;
        return false;
      }
    })

    if (contain) {
      //选中除开不计免赔的项目
      if (index.indexOf("BuJiMian") < 0) {
        if (data > 0) {
          var input_obj = $("input[data-value=" + index + "]").parent(".choose");
          var rels;
          //如果是交强险 1 2代表有选中交强
          if (index == 'ForceTax' && (data == '1' || data == '2')) {
            //相当于continue 跳出循环，因为默认选中，再次选中则取消 所以阻止
            return true;
          }
          console.log('选中细项', index, data)
          //以下5项需要匹配选中金额
          if (index == 'HuaHen' || index == 'SanZhe' || index == 'SiJi' || index == 'ChengKe' || index == 'BoLi') {
            rels = aotuChooseVal(data, index);
          }
          choose(input_obj, true, rels);
        }

      } else {
        //不计免赔项目
        var id = index.replace(/BuJiMian/, "");
        if (data > 0) {
          bujiOpen(id);
        } else {
          //不计免赔没选中的情况下，对应主线有选中 则关闭不计免赔
          if (insuranceArr[id] > 0) {
            bujiClose(id);
          }

        }

      }
    }
  })
  console.info('------------------------自动选中保险完成-----------------------');
  //选择弹出框
  $('.price').click(function () {
    var id = $(this).attr('id');
    //默认选中的类型(文字)
    var defalut_html = $('input[name="' + id + '[]"]:eq(0)').attr("value");
    //是否默认选中不计免赔
    var defalut_buji = $('input[name="' + id + '[]"]:eq(1)').attr("value") || 0;
    if (_plan['BuJiMian' + id] == 1 || defalut_buji == 1) {
      var buji = 1;
    } else {
      var buji = 0;
    }
    console.log(id);

    var html = '';
    //这个几个险有上部分选择
    if (id == 'BoLi' || id == "HuaHen" || id == "SanZhe" || id == "SiJi" || id == "ChengKe") {
      html += '<div class="choose_price">';
      if (id == 'BoLi') {
        html += '<h1>选择类型</h1>';
      } else {
        html += '<h1>投保金额</h1>';
      }
      html += '<div class="price_b">';
      html += '<ul>';
      //获取对应的选择类型 枚举
      var obj2 = eval("(" + id + ")");//字符串转换为对象
      console.log(obj2);
      $.each(obj2, function (index, data) {
        //默认选中
        if (index == defalut_html) {
          html += '<li onclick="addtype(this)" class="current" data-target="' + id + '" value-code="' + data + '">' + index + '</li>';
        } else {
          html += '<li onclick="addtype(this)" data-target="' + id + '" value-code="' + data + '">' + index + '</li>';
        }
      })
      html += '</ul>';
      html += '</div>';
      html += '</div>';

    }
    if (BuJiMain.toString().indexOf(id) > -1) {
      var BuJiMianMark = 1;
    } else {
      var BuJiMianMark = 0;
    }
    if (BuJiMianMark) {
      html += '<div class="free">';
      html += '<h1> 不计免赔</h1>';
      html += '<div class="price_b">';
      html += '<ul>';
      if (buji == 1) {
        html += '<li onclick="addtype(this)" class="current" data-target="' + id + '">需要</li>';
        html += '<li onclick="addtype(this)" class="" data-target="' + id + '">不需要</li>';
      } else {
        html += '<li onclick="addtype(this)" class="" data-target="' + id + '">需要</li>';
        html += '<li onclick="addtype(this)" class="current" data-target="' + id + '">不需要</li>';
      }

      html += '</ul>';
      html += '</div>';
      html += '</div>';
    }
    html += '<div class="foot_button hiden_div" onclick="hiden_div(this)" data-target="' + id + '">确定</div>';
    $(".choose_item").html(html);
    $('.choose_item').animate({ bottom: "0px" }, 150);
    $('#mask').show();
    $('.con').css({ overflowY: 'hidden' });//IOS会滚动

    //默认把选中项 生成回调方法
    var defalutone = $('.choose_item .choose_price .price_b .current');
    var defaluttwo = $('.choose_item .free .price_b .current');
    if (defalutone.length == 1) {
      addtype(defalutone);
    }
    if (defaluttwo.length == 1) {
      addtype(defaluttwo);
    }


  });

  /*用户手动终止请求  关闭报价框*/
  $('.layer-close-button').click(function () {
    $('.con').css({ overflowY: 'auto' });//IOS会滚动
    $('#load-layer,.load-bg,#two h2, .emissions').hide();
    win_closed = true;
    clearTimeout(network);
    var rand = 0;
    if (total_num == 1) {
      $("#styled" + 'one').html(rand + '%');
      $('#progress' + 'one').css('width', rand + '%');
      $("#progress" + 'one').attr("data-percent", rand);
    } else if (total_num == 2) {
      $("#styled" + 'two').html(rand + '%');
      $('#progress' + 'two').css('width', rand + '%');
      $("#progress" + 'two').attr("data-percent", rand);
    } else {
      $("#styled" + 'three').html(rand + '%');
      $('#progress' + 'three').css('width', rand + '%');
      $("#progress" + 'three').attr("data-percent", rand);
    }
    $('.loading').attr('src', loading_img);

  });

  /*排量的选择[单选]*/
  $('.emissions_options li').click(function () {
    var isSelect = $(this).find('img').attr('src').indexOf('choosed') > 0;

    $('.emissions_options li img').prop('src', '/Public/static/image/api/static.png');
    if (isSelect) {

      window.emissions = undefined;
      $(this).find('img').prop('src', '/Public/static/image/api/static.png');
    } else {

      $(this).find('img').prop('src', '/Public/static/image/api/choosed.png');
      window.emissions = $(this).attr('data-value');/*储存选中的排量值*/
    }
  });
  /*排量选完 确定*/
  $('#emissions .emissions_ok').click(function () {
    if (typeof emissions === 'undefined') {
      layer.open({
        content: '请选择一个排量',
        time: 2
      });
    } else {
      $('#emissions').hide(300, function () {
        submitFm();
      });
    }
  });
  // $('#tel-fill .emissions_ok').click(function () {
  //   var tel = $('#tel-fill input[name="tel"]').val()
  //   if (tel === '') {
  //     layer.open({
  //       content: '请输入手机号',
  //       time: 2
  //     });
  //   } else if (!reg('tel', tel)) {
  //     layer.open({
  //       content: '请输入正确的手机号',
  //       time: 2
  //     });
  //   } else {
  //     $('#emissions').hide(300, function () {
  //       submitFm();
  //       window.JSON1.tel = tel
  //       window.JSON1.openId = vueRoute && vueRoute.params.open_id
  //       layer.open({type: 2,shadeClose:false});/*过渡*/
  //       $.post(
  //         '/Xiaochengxu/CarInsurance/saveOrder'
  //         , JSON1
  //         , (res) => {
  //           layer.closeAll();/*关闭过渡*/
  //           if (res.code == 0) {
  //             window.vueRouter.push({
  //               name: 'CarInsurance__Result',
  //               params: vueRoute && vueRoute.params
  //             })
  //           } else {
  //             layer.open({
  //               content: res.msg,
  //               time: 2
  //             });
  //           }
  //         }
  //       , 'json')
  //     });
  //   }
  // });

});

function is_ML() {
  // is_emissions=true;
  console.log('是否需要选择排量', _plan.ForceTax != 0 && !!is_emissions);

  if (_plan.ForceTax != 0 && !!is_emissions) {//需要选择 排量[厦门车型]
    $('#load-layer').fadeIn(150, function () {/*遮罩*/
      $('#emissions').show()
    })
  // } else if(location.pathname.match('Xiaochengxu/CarInsurance/editInsurance')) {
  //   submitFm();
  //   if (vueRoute) {
  //     window.JSON1.openId = vueRoute.params.open_id
  //     window.JSON1.order_sn = vueRoute.params.order_sn
  //   }
  //   layer.open({type: 2,shadeClose:false});/*过渡*/
  //   $.post(
  //     '/Xiaochengxu/CarInsurance/updateOldInsurance'
  //     , JSON1
  //     , (res) => {
  //       layer.closeAll();/*关闭过渡*/
  //       if (res.code == 0) {
  //         window.vueRouter.push({
  //           name: 'CarInsurance__Result',
  //           params: vueRoute && vueRoute.params
  //         })
  //       } else {
  //         layer.open({
  //           content: res.msg,
  //           time: 2
  //         });
  //       }
  //     }
  //   , 'json')
  // } else if (isWechat) { // 微信里面
  //   $('#load-layer').fadeIn(150, function () {/*遮罩*/
  //     $('#tel-fill').show()
  //   })
  } else {
    submitFm()
  }
}
//选中保险公司
function changeCompany(type, judges) {
  if (~~type == 4 && parseInt(judges, 10) == 0) {//投保期未到2017.1.1 人保公司无法报价
    confirm_popbox('投保期未到2017.1.1<br>人保保险公司暂时无法报价投保');
    return;
  }

  if (arr1.indexOf(type) == '-1') {
    arr1.push(type);

  } else {
    arr1.remove(type);
  }
  var obj = $('.insur' + type);
  obj.toggleClass('select-state');
  var img = obj.find('img');

  if (img.hasClass('on')) {
    img.removeClass('on').attr('src', img.attr('data-off'))
  } else {
    img.addClass('on').attr('src', img.attr('data-on'))
  }
  console.log(arr1);
}
Array.prototype.remove = function (val) {
  var index = this.indexOf(val);
  if (index > -1) {
    this.splice(index, 1);
  }
};
//自动匹配金额 选中对应项
function aotuChooseVal($int, ord) {
  var choose = [];
  var obj2 = eval("(" + ord + ")");
  var last = [];
  $.each(obj2, function (k, r) {

    if (parseInt($int) <= parseInt(r)) {

      choose[0] = k;
      choose[1] = r;

      return false;
    } else {
      last[0] = k;
      last[1] = r;
    }

  })
  if (choose[1] == '' || choose[1] == undefined) {
    choose[0] = last[0];
    choose[1] = last[1];
  }
  return choose;
}


//单选赋值  status == false仅当勾选时触发
//status == true 强制选中并且不选中不不计免赔 false 选中默认的不计免赔
// rels 存在则选中金额
function choose(_self, status, rels) {
  var src = _self.find('img').attr("src");
  var str = "";
  var value = _self.find('input').attr('data-value');

  if (BuJiMain.toString().indexOf(value) > -1) {
    var BuJiMianMark = 1;
  } else {
    var BuJiMianMark = 0;
  }

  if (src == unchoose_img || status == true) {
    //选中
    console.log('选中险种类型', value);
    str = choose_img;
    if (value == 'CheSun') {
      showSome(true);

    }
    //自动选中父级车险
    if (_self.parents('dl').hasClass('hidden')) {
      $('dl[data-value=CheSun] .choose img').attr('src', str);
      _plan['CheSun'] = '1';
    }

    if (rels == '' || rels == undefined) {
      //选中后的值
      var defalut = $('input[name="' + value + '[]"]:eq(0)').attr("value-code");
      _plan[value] = defalut ? defalut : '1';
    } else {
      _plan[value] = rels[1];
      $("#" + value).children('span').html(rels[0]);
      $("#" + value).children('span').next('input').val(rels[0]);
    }
    //仅当勾选时触发  选中是否默认选中不计免赔
    if (status != true) {
      var buji = $('input[name="' + value + '[]"]:eq(1)').attr("value");
      if (buji == 1) {
        bujiOpen(value);
      }

    }
  } else {
    console.log('取消险种类型', value);
    str = unchoose_img;
    if (value == 'CheSun') {
      showSome(false)
    }
    _plan[value] = '0';
    if (BuJiMianMark) {
      _plan['BuJiMian' + value] = '0';
    }
  }
  console.log('车险明细', _plan);
  _self.find('img').attr("src", str);
}
//显示或者隐藏 玻璃单独破碎险  涉水险  车身划痕险 自燃损失险
function showSome(type) {
  if (type) {
    $('.lala').show();
    $('.hidden').show();
  }
  else {
    $('.lala').hide();
    $('.hidden').hide();
    $('.hidden').each(function (i, a) {
      $(this).find('.choose img').prop('src', unchoose_img);
    })
    //清楚对应项目
    _plan['ZiRan'] = '0';
    _plan['BuJiMianZiRan'] = '0';
    _plan['BoLi'] = '0';
    _plan['SheShui'] = '0';
    _plan['BuJiMianSheShui'] = '0';
    _plan['HuaHen'] = '0';
    _plan['BuJiMianHuaHen'] = '0';
    _plan['HcSanFangTeYue'] = '0';
  }
  return true;
}

function bujiOpen(id) {
  console.log('bujiOpen', id);
  $(".freeIcon_" + id).attr('src', buji_img);
  $(".freeIcon_" + id).addClass("freeIcon");
  $(".freeIcon_" + id).show();
  $('input[name="' + id + '[]"]:eq(1)').attr("value", 1);
  _plan['BuJiMian' + id] = '1';
}

function bujiClose(id) {
  console.log('bujiClose', id);
  $(".freeIcon_" + id).removeClass("freeIcon");
  $(".freeIcon_" + id).hide();
  $('input[name="' + id + '[]"]:eq(1)').attr("value", 0);
  _plan['BuJiMian' + id] = '0';
}

//点击弹出框的修改
function addtype(obj) {
  var id = $(obj).attr("data-target");
  var span_obj = $("#" + id).children(":first");
  var lis = $(obj).html();
  $(obj).addClass('current').siblings().removeClass('current');
  if (lis == '需要' || lis == '不需要') {
    callbackfunctionBuji = function () {
      if (lis == '需要') {
        bujiOpen(id);
      } else {
        bujiClose(id);
      }
    }

  } else {
    //选择金额
    callbackfunction = function () {
      var code = $(obj).attr("value-code");
      $('input[name="' + id + '[]"]:eq(0)').attr("value", lis).attr("value-code", code);
      _plan[id] = code;
      span_obj.html(lis);
    }
  }
}
//弹出框确定按钮
function hiden_div(obj) {
  callbackfunctionBuji();
  callbackfunction();

  var id = $(obj).attr("data-target");
  var choose_obj = $(".widthprice input[data-value=" + id + "]").parent('.widthprice');

  choose(choose_obj, true);

  $('#mask').click();
}
//生成随机校验串
function randomString(len) {
  len = len || 32;
  var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  var maxPos = $chars.length;
  var pwd = '';
  for (var i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}

function popup(o) {//个人和公司的切换//0:个人，1:公司//需要传入字符串0|1

  var str = ['个人', '公司'];
  if (!!o) {
    evaluation(o);
    return;
  }

  $('#mask').fadeIn();
  $('.con').css({ overflowY: 'hidden' });//IOS会滚动
  $('.popup').animate({ bottom: 0 }, 150).find('div').click(function () {
    var i = $(this).index();
    i < 2 && evaluation(i);
    $('.popup').stop().animate({ bottom: '-158px' }, 150);
    $('#mask').fadeOut();
    $('.con').css({ overflowY: 'auto' });//IOS会滚动
  });

  $('#mask').click(function () {
    $(this).fadeOut();
    $('.con').css({ overflowY: 'auto' });//IOS会滚动
    $('.popup').stop().animate({ bottom: '-158px' }, 150);
  });

  function evaluation(ix) {
    $('.car_type span:last').html(str[ix]);
  }
}

window.is_ML = is_ML;
window.changeCompany = changeCompany;
window.choose = choose;
window.bujiOpen = bujiOpen;
window.bujiClose = bujiClose;
window.addtype = addtype;
window.hiden_div = hiden_div;
window.randomString = randomString;
window.popup = popup;
