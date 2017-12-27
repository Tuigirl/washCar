import './login.styl'

$(document).ready(function () {
  var containerMtop = -$('#container').height() / 2 + 'px'
  var containerMleft = -$('#container').width() / 2 + 'px'
  $('#container').css({ // 位置计算
    marginTop: containerMtop,
    marginLeft: containerMleft
  })

  if ($.cookie("rmbUser") == "true") {
    $("#rememb").addClass("checked")
    $('input[name=username]').val($.cookie("username"));
    $('input[name=password]').val($.cookie("password"));
  }

  $('[name="username"]').focus()
  aniEndClearClassName(document.querySelector('ul'))
});

$(document).ready(function () {
  $(document).on('keydown', function (e) {
    if (e.keyCode == 13)
      $('.form__button').click()
  })

  $('#rememb').on('click', function () {
    $(this).toggleClass('checked')
  })

  $('.form__button').on('click', function () {
    var username = $('input[name=username]').val();
    var password = $('input[name=password]').val();

    if (username == '') {
      $('ul').addClass('animated shake')
      $('input[name=username]').parent().attr('alt', '用户名不能为空')
      return false
    } else {
      $('input[name=username]').parent().attr('alt', '')
    }
    if (password == '') {
      $('ul').addClass('animated shake')
      $('input[name=password]').parent().attr('alt', '密码不能为空')
      return false
    } else {
      $('input[name=password]').parent().attr('alt', '')
    }

    $.post(
      '/Index/login?dosubmit=1'
      , $('form').serialize()
      , function (res) {
        console.log(res)

        if (res.status == 0) { //登录成功
          $('li').attr('alt', '')

          //记住用户名密码
          if ($("#rememb").hasClass("checked")) {
            $.cookie("rmbUser", "true", { expires: 7 }); //存储一个带7天期限的cookie
            $.cookie("username", username, { expires: 7 });
            $.cookie("password", password, { expires: 7 });
          }
          else {
            $.cookie("rmbUser", "false", { expire: -1 });
            $.cookie("username", "", { expires: -1 });
            $.cookie("password", "", { expires: -1 });
          }

          location.href = res.url
        } else if (res.status == 1) { //用户名错误
          $('ul').addClass('animated shake')
          $('input[name=username]').parent().attr('alt', res.info)
        } else if (res.status == 2) { //密码错误
          $('ul').addClass('animated shake')
          $('input[name=password]').parent().attr('alt', res.info)
        } else if (res.status == 999) { //维护
          alert('系统正在升级维护');
          return false;
        } else {
          alert('登录失败,请联系管理员')
        }
      }
      , 'json')

  })
})

function aniEndClearClassName(el) {
  var animationEvent = whichAnimationEvent();
  /* 监听变换事件! */
  animationEvent &&
    el.addEventListener(animationEvent, function () {
      this.className = ''
    });
}
function whichAnimationEvent() {
  var t, el = document.createElement('fakeelement'),
    animations = {
      'animation': 'animationend',
      'OAnimation': 'oAnimationEnd',
      'MozAnimation': 'animationend',
      'WebkitAnimation': 'webkitAnimationEnd'
    }
  for (t in animations) {
    if (el.style[t] !== undefined) {
      return animations[t];
    }
  }
}