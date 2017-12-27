/*内容区域 高度自适应*/
$(function(){
    var winHeight = $(window).height();
    var headerHeight = $('header').outerHeight(!0);
    var foot_button = $('.foot_button').length>0?$('.foot_button').outerHeight(!0):0;
    var myHeight = winHeight-headerHeight-foot_button;
    $('.con').outerHeight(myHeight);
    $('.con').css('overflow-y','auto');
    //眼睛睁闭
    $('#eye').on('click',function(){
        $('.discount-price').stop().slideToggle(0);
        // $('.normal').stop().slideToggle(0);
        var img = $(this).find('img');

        if(img.attr('src').match(/(closeEye)/)){
            img.attr('src', img.data('on'));
        }else{
           img.attr('src', img.data('off'));
        }
    });
});
/*价格拆分重组
*{$pars} 传值jQuery对象
*拆分前<span>4999.08</span>
*拆分后<span>4999.</span><span>08</span>
*/
function  priceRecombine($pars) {
    $pars.each(function(){
       var ob = !$(this).text().trim()? ['0','00'] :$(this).text().replace(/\s+/g,'').split('.');
       if (ob.length && ob[0].trim()!='-----' && $(this).next('span').size()==0) {
          ob[1] = !ob[1] ? '00' : ob[1].length < 2 ? ob[1] + '0' : ob[1];
          // ob[1] += !$(this).parents('.foot_button').size()?'':' &nbsp; 元';
          $(this).text(ob[0]).after('<span>.'+ ob[1] +'</span>');
       }
    });
}
/*将CSS属性转为数值
*{$obj} 传值jQuery对象
*{attribute} 属性名
*/
function size ($obj, attribute) {
    return parseInt($obj.css(attribute).replace('px', ''));
}

/**
*layer弹窗扩展 带确定按钮
*/
function confirm_popbox (content) {
    var btnHTML = '<button style="display: block;width: 100%;text-align: center;margin: 0 auto;height: 35px;border-top: 1px solid #e5e5e5;">确定</button>';
    layer.open({content: content});
    $('.layermmain').css('zIndex', 1000).find('.layermchild').append(btnHTML).find('button').click(function() {
        $('.layermbox').remove();
    });
}

/**
*JQ扩展 POST方式跳转URL
*url:要跳转的url,args:附加参数,type:get|post
*/
$.extend({
    JumpURL:function(url,args,type){

        var form = $("<form method='"+ type +"'></form>"),
            input, type = type||'get'
        form.attr({"action":url});
        $.each(args,function(key,value){
            input = $("<input type='hidden'>");
            input.attr({"name":key});
            input.val(value);
            form.append(input);
        });

        form.appendTo(document.body);
        form.submit();
        document.body.removeChild(form[0]);
    }
});
