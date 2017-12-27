/*公共加载js*/

//预定义目录跟路径
var ajaxRoot = "/insurance/";
//校验函数
function checkMobile(mobile) {
    if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/i.test(mobile)) {
        return false;
    } else {
        return true;
    }
}

function setLayer(_self) {
    var allWidth = $('body').width();
    var _selfWidth = _self.width();
    var allHeight = $('body').height();
    var _selfHeight = _self.height();
    var left = (allWidth - _selfWidth) / 2;
    var top = (allHeight - _selfHeight) / 2;
    if (left < 5) { left = 5 }
    if (top < 5) { $('body').height(_selfHeight);
        top = 5; }
    _self.css('left', left + 'px');
    _self.css('top', top + 'px');
    $('.gray').show();
    _self.show();
}

function setPop(_self) {

    var allWidth = $('body').width();
    var _selfWidth = _self.width();
    var allHeight = $('body').height();
    var _selfHeight = _self.height();
    var left = (allWidth - _selfWidth) / 2;
    var top = (allHeight - _selfHeight) / 2;

    _self.window('open').window('resize', { top: top, left: left });
}

$(function() {
    $('body').on('click', '.close_btn', function() {
        $(this).parents('.window_pop').hide();
        $('.gray').hide();
    });
    $('body').on('hover', '.close_btn', function() {
        $(this).toggleClass('close_btn_over');
    });

    /*拖拽*/
    dragAndDrop(".window_pop2");
    dragAndDrop(".window_pop");

    function dragAndDrop(s) {
        var _move = false; //移动标记
        var _x, _y; //鼠标离控件左上角的相对位置
        $(s).find('.top').mousedown(function(e) {
            _move = true;
            _x = e.pageX - parseInt($(s).css("left"));
            _y = e.pageY - parseInt($(s).css("top"));
        });
        $(document).mousemove(function(e) {
            if (_move) {
                var x = e.pageX - _x; //移动时鼠标位置计算控件左上角的绝对位置
                var y = e.pageY - _y;
                $(s).css({ top: y, left: x }); //控件新位置
            }
        }).mouseup(function() {
            _move = false;
        });
        var ileft = ($(document).width() - $(s).width()) / 1.8;
        //初始化拖拽div的位置
        var itop = ($(window).height() - $(s).height()) / 2;
        $(s).css({ left: ileft });
        $(s).css({ top: itop });
    }
})

//百度统计
//var _hmt = _hmt || [];
//(function() {
//  var hm = document.createElement("script");
//  hm.src = "//hm.baidu.com/hm.js?f02acb9b9236d3dbcb1602b5b670a7f0";
//  var s = document.getElementsByTagName("script")[0];
//  s.parentNode.insertBefore(hm, s);
//})();

/*iframe 重置高度*/
// $(function () {
//     var timer = setInterval(function () {
//         if ($('.datagrid-wrap').length) {
//             reSizeH();
//             clearInterval(timer);
//         }
//     }, 100);
//
//     function reSizeH(){
//         var _1e1 = $('.datagrid-wrap'),
//             _1e2 = _1e1.find('.datagrid-view'),
//             _1e3 = _1e2.find('.datagrid-body'),
//             dH = $('body')[0].scrollHeight, tH = 0;
//             ttH = _1e1.prev().outerHeight(!0),
//             mtH = _1e1.parents('.i_box').outerHeight(!0) - _1e1.parents('.i_box').innerHeight();
//
//             $('body').find('>div:not(.i_box):visible').each(function (i, obj) {
//                 tH += $(obj).outerHeight(!0);
//             });
//
//         var zH1 = dH - tH - ttH - mtH,
//             zH2 = zH1 - $('.pagination').outerHeight(!0),
//             zH3 = zH2 - $('.datagrid-header-inner').outerHeight(!0);
//
//         _1e1._outerHeight(zH1);
//         _1e2._outerHeight(zH2);
//         _1e3._outerHeight(zH3);
//
//     }
// });

/*弹窗关闭按钮 遮罩的隐藏*/
$(function() {
    $('.panel-tool-close').click(function() {
        $('.gray').size() > 0 && $('.gray').hide();
    });
});

function show_excel() {
    $('.gray').show();
    setLayer($('#pop'));
    $("#endtime").val('').datebox('setValue', '');
    $("#starttime").val('').datebox('setValue', '');
}

function ajaxLoading() {
    $("<div class=\"datagrid-mask\"></div>").css({ display: "block", width: "100%", height: $(window).height() }).appendTo("body");
    $("<div class=\"datagrid-mask-msg\"></div>").html("正在处理，请稍候。。。").appendTo("body").css({ display: "block", left: ($(document.body).outerWidth(true) - 190) / 2, top: ($(window).height() - 45) / 2 });
}

function ajaxLoadEnd() {
    $(".datagrid-mask").remove();
    $(".datagrid-mask-msg").remove();
}

function change_BD(is_name) {
    $.get('../Statistical/getCityBdLsist', { city_id: $("#city_id").val() }, function(response) {
        response = JSON.parse(response);
        if (response.code == 0) {
            var dom = "";
            $('#BD_id option:not(:first)').remove();

            $.each(response.data, function(i, v) {
                dom += '<option value=' + (is_name ? v.user_name : v.id) + '>' + v.user_name + '</option>'
            })
            $('#BD_id').append(dom);
        }
    })
}
/**
 *easyui 日期组件的扩展
 */
function easyui_datebox_reset(_starttime_, _endtime_) {
    var buttons = $.extend([], $.fn.datebox.defaults.buttons);
    buttons.splice(1, 0, {
        text: '清除',
        handler: function(target) {
            $(target).val('').datebox('setValue', '');
        }
    });
    $('.easyui-datebox').datebox({
        buttons: buttons
    });
    if (_starttime_ && _endtime_) {
        $(_starttime_).datebox({
            onSelect: function(date) {
                var enddate = $(_endtime_ + '+span>input').val();
                var starttime = date.toLocaleDateString().replace(/\//g, '-');

                if (enddate != '') {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (starttime > enddate) {
                        $(this).val(enddate).datebox('setValue', enddate);
                    }
                }
            },
            onChange: function(date) {
                var enddate = $(_endtime_ + '+span>input').val();
                var starttime = date;
                if (enddate != '') {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (starttime > enddate) {
                        var _ = this;
                        setTimeout(function() {
                            $(_).val(enddate).datebox('setValue', enddate);
                        }, 0)
                    }
                }
            }
        })
        $(_endtime_).datebox({
            onSelect: function(date) {
                var starttime = $(_starttime_ + '+span>input').val();
                var enddate = date.toLocaleDateString().replace(/\//g, '-');

                if (starttime == '') {
                    alert('请选择开始时间');
                    $(this).val('').datebox('setValue', '');
                } else {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (enddate < starttime) {
                        $(this).val(starttime).datebox('setValue', starttime);
                    }
                }
            }
        })
    }
}

/**
 *easyui 日期时间组件的扩展
 */
function easyui_datetimebox_reset(_starttime_, _endtime_) {
    if (_starttime_ && _endtime_) {
        $(_starttime_).datetimebox({
            onSelect: function(date) {
                var enddate = $(_endtime_ + '+span>input').val();
                var starttime = date.toLocaleDateString().replace(/\//g, '-');

                if (enddate != '') {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (starttime > enddate) {
                        $(this).val(enddate).datebox('setValue', enddate);
                    }
                }
            },
            onChange: function(date) {
                var enddate = $(_endtime_ + '+span>input').val();
                var starttime = date;
                if (enddate != '') {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (starttime > enddate) {
                        var _ = this;
                        setTimeout(function() {
                            $(_).val(enddate).datebox('setValue', enddate);
                        }, 0)
                    }
                }
            }
        })
        $(_endtime_).datetimebox({
            onSelect: function(date) {
                var starttime = $(_starttime_ + '+span>input').val();
                var enddate = date.toLocaleDateString().replace(/\//g, '-');

                if (starttime == '') {
                    alert('请选择开始时间');
                    $(this).val('').datebox('setValue', '');
                } else {
                    starttime = starttime.split('-')[0] + '-' + (starttime.split('-')[1].length < 2 ? '0' + starttime.split('-')[1] : starttime.split('-')[1]) + '-' + (starttime.split('-')[2].length < 2 ? '0' + starttime.split('-')[2] : starttime.split('-')[2]);
                    enddate = enddate.split('-')[0] + '-' + (enddate.split('-')[1].length < 2 ? '0' + enddate.split('-')[1] : enddate.split('-')[1]) + '-' + (enddate.split('-')[2].length < 2 ? '0' + enddate.split('-')[2] : enddate.split('-')[2]);
                    if (enddate < starttime) {
                        $(this).val(starttime).datebox('setValue', starttime);
                    }
                }
            }
        })
    }
}

/**
 * 获取url中的参数,返回JSON对象
 */
function GetUrlRequest() {
    var url = decodeURI(location.search);
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1),
            strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0].trim()] = strs[i].split("=")[1].trim()
        }
    }
    return theRequest;
}

$(function() {
    if($.cookie('username') == 'zhangzhi')
        $('.ican button:contains(Excel),.ican button:contains(excel)').hide()
})