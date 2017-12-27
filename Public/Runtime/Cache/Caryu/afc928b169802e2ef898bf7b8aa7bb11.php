<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>驾遇<?php echo C('SYSTEM_NAME');?> - 用户登录</title>
	<script src="/wechat/Public/static/js/jquery.min.js" type="text/javascript"></script>
	<script src="/wechat/Public/static/js/jquery.cookie.js" type="text/javascript"></script>
	<link rel="stylesheet" href="//cdn.bootcss.com/animate.css/3.5.2/animate.min.css"/>
	<style type="text/css">
		.noselect {
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none; /* Chrome/Safari/Opera */
			-khtml-user-select: none; /* Konqueror */
			-moz-user-select: none; /* Firefox */
			-ms-user-select: none; /* Internet Explorer/Edge */
			user-select: none; /* Non-prefixed version, currently
			not supported by any browser */
		}
    body, ul, li, h3, input{
        margin: 0;
        padding: 0;
    }
    body{
        color: #6C6C6C;
        font-size: 15px;
        background: url('/Public/static/image/caryu/icon/background.png');
				font-family: '微软雅黑';
    }
    li{list-style: none}
		a{color: inherit;text-decoration: none}
		a:hover{text-decoration: underline}
		img{display: block;width: 100%}
    input{border: none}
		input:focus{outline: none}
    input::-webkit-input-placeholder{color:#CFCFCF}
    input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0px 1000px white inset;
    }
		#container{
			width: 375px;
			margin: 0 auto;
			position: absolute;
			left: 50%;top: 50%;
		}
			.logo{
				width: 140px;
				height: 140px;
				margin: 0 auto;
			}
			.title h3{
				text-align: center;
				font-size: 40px;
				line-height: 40px;
				margin: 25px 0 40px;
			}
			form{

			}
				ul{
					border: 5px solid #E2E2E2;
					border-radius: 12px;
					margin: 0 auto;
				}
					li{
						height: 60px;
						position: relative;
					}
					li:first-child{
						border-bottom: 1px dashed #E2E2E2;
					}
					li:before{
						content: "";
						display: block;
						float: left;
						height: 100%;
						width: 21%;
						background-image: url('/Public/static/image/caryu/icon/icon_login.png');
						background-repeat: no-repeat;
						background-color: #F5F5F5;
					}
					li:first-child:before{
						background-position: left top;
						border-radius: 6px 0 0 0;
					}
					li:last-child:before{
						background-position: left bottom;
						border-radius: 0 0 0 6px;
					}
					li:after{
						content: attr(alt);
						color: #FF3C00;
						display: block;
						position: absolute;
						left: 100%;top: 50%;
						white-space: nowrap;
						margin-top: -10px;
						padding-left: 1em;
					}
						li input{
							display: block;
							float: left;
							height: 100%;
							width: 79%;
							outline: none;
							padding-left: .75em;
							box-sizing: border-box;
							-webkit-box-sizing: border-box;
								-moz-box-sizing: border-box;
									-ms-box-sizing: border-box;
										-o-box-sizing: border-box;
						}
						li input[name="username"]{
							border-radius: 0 6px 0 0;
						}
						li input[name="password"]{
							border-radius: 0 0 6px 0;
						}
				.form__rememb{
					text-align: right;
					margin: 6px 0;
				}
					#rememb{
						overflow: hidden;
						display: inline-block;
						padding-right: .75em;
						cursor: pointer;
						position: relative;
					}
					#rememb:before{
						content: "";
						display: block;
						float: left;
						margin-right: .5em;
						width: 16px;
						height: 16px;
						text-align: center;
						border-radius: 3px;
						border: 2px solid #E2E2E2;
						background-color: white;
					}
					#rememb.checked:after{
						content: "";
						width: 10px;
						height: 10px;
						position: absolute;
						background-color: #FFBB00;
						left: 5px;
						top: 5px;
						border-radius: 50%;
					}

				.form__button input{
					display: block;
					width: 100%;
					color: white;
					font-size: 24px;
					line-height: 60px;
					background-color: #FFBB00;
					border-radius: 12px;
					cursor: pointer;
					-webkit-transition: all .25s ease-in-out;
			        -moz-transition: all .25s ease-in-out;
			            -o-transition: all .25s ease-in-out;
			                transition: all .25s ease-in-out;
				}.form__button input:hover{
					background-color: #dea403;
					-webkit-transition: all .25s ease-in-out;
			        -moz-transition: all .25s ease-in-out;
			            -o-transition: all .25s ease-in-out;
			                transition: all .25s ease-in-out;
				}
			.tel{
				margin-top: 6px;
				text-align: center;
			}
	</style>
</head>
<body>
	<div id="container">
		<div class="logo animated flipInX"><img src="/wechat/Public/static/image/caryu/logo.png"></div>
		<div class="title animated flipInX"><h3>驾遇<?php echo C('SYSTEM_NAME');?></h3></div>
		<form>
			<ul class=" animated flipInX">
				<li><input name="username" type="text" placeholder="请输入用户名" maxLength="16" autocomplete="off"></li>
				<li><input name="password" type="password" placeholder="请输入密码" maxLength="18" autocomplete="off"></li>
			</ul>
			<div class="form__rememb animated flipInX">
				<span id="rememb" class="noselect">记住密码</span>
			</div>
			<div class="form__button animated flipInX">
				<input type="button" value="登录">
			</div>
		</form>
		<div class="tel animated flipInX">
			热线电话：<a href="tel:01059564072">010-59564072</a>
		</div>
	</div>
	<script type="text/javascript">
    $(document).ready(function () {
			var containerMtop = -$('#container').height()/2+'px'
			var containerMleft = -$('#container').width()/2+'px'
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
			$(document).on('keydown', function(e) {
				if (e.keyCode == 13)
					$('.form__button').click()
			})

			$('#rememb').on('click', function() {
				$(this).toggleClass('checked')
			})

			$('.form__button').on('click', function() {
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
					'<?php echo U("Index/login?dosubmit=1");?>'
					, $('form').serialize()
					, function(res) {
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
						} else{
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
				el.addEventListener(animationEvent, function() {
					this.className = ''
				});
		}
		function whichAnimationEvent(){
		    var t, el = document.createElement('fakeelement'),
		        animations = {
		          'animation':'animationend',
		          'OAnimation':'oAnimationEnd',
		          'MozAnimation':'animationend',
		          'WebkitAnimation':'webkitAnimationEnd'
		    }
		    for(t in animations){
		        if( el.style[t] !== undefined ){
		            return animations[t];
		        }
		    }
		}
	</script>
</body>
</html>