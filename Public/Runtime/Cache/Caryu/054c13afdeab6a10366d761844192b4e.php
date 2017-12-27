<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href="/wechat/Public/static/js/uicss/ligerui-all.css" rel="stylesheet" type="text/css">
<link href="/wechat/Public/static/js/uicss/easyui.css" rel="stylesheet" type="text/css">
<link href="/wechat/Public/static/js/uicss/icon.css" rel="stylesheet" type="text/css">
<link href="/wechat/Public/static/js/uicss/ligerui-tab.css" rel="stylesheet" type="text/css">
<link href="/wechat/Public/static/js/uicss/ligerui-layout.css" rel="stylesheet" type="text/css">
<link href="/wechat/Public/static/js/uicss/ligerui-menu.css" rel="stylesheet" type="text/css">
<!-- <link href="/wechat/Public/static/js/uicss/style.css" rel="stylesheet" type="text/css"/> -->

	<title>汽车美容</title>
	<script type="text/template" id="menu-data">
		<?php echo json_encode($meau) ?>
	</script>
  <script type="text/template" id="city-list">
		<?php echo json_encode($city_list) ?>
  </script>
<link href="/Public/static/packaged-assets/Caryu/css/main-5cb33d1e.css" rel="stylesheet"><link href="/Public/static/packaged-assets/Caryu/css/Index\index-58ed2662.css" rel="stylesheet"><script type="text/javascript" src="/Public/static/packaged-assets/Caryu/./js/vendor.6f6f6f54.js"></script><script type="text/javascript" src="/Public/static/packaged-assets/Caryu/js/Index\index-a12fb7ecd635c19f0c38.js"></script></head>
	<script type="text/javascript" src="/wechat/Public/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/wechat/Public/static/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/wechat/Public/static/js/jquery.json.min.js"></script>
<script type="text/javascript" src="/wechat/Public/static/js/easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/wechat/Public/static/js/easyui/easyui-lang-zh_CN.js"></script>

<script src="/wechat/Public/static/js/uijs/jquery-ui.js" type="text/javascript"></script>
<script src="/wechat/Public/static/js/uijs/function.js" type="text/javascript"></script>
<script src="/wechat/Public/static/js/uijs/ligerBuild.js" type="text/javascript"></script>
	<script type="text/javascript" src="/wechat/Public/static/js/formvalidator/formValidator.min.js"></script>
<script type="text/javascript" src="/wechat/Public/static/js/formvalidator/formValidatorRegex.js"></script>
<script type="text/javascript">initConfig_setting.theme = 'App';</script>
	<script type="text/javascript">
		var layout = null, tab = null, accordion = null, tree = null;
		var topHeight = 84, leftWidth = 160;
		$(function() {
			//页面布局
			layout = $("#global_layout").ligerLayout({
				topHeight: topHeight,
				leftWidth: leftWidth,
				height: '100%',
				bottomHeight: 0,
				allowTopResize: false,
				allowLeftResize: false,
				allowBottomResize: false,
				allowLeftCollapse: false,
				// isLeftCollapse: true,
				space: 0,
				onHeightChanged: f_heightChanged
			});
			var height = $(".l-layout-center").height();
			$("#divHome").height(height);
			//Tab
			$("#gl-center").ligerTab({
				height : height
			});
			//左边导航面板
			$("#gl-left-nav").ligerAccordion({
				height : height - 0,
				speed : null
			});

			tab = $("#gl-center").ligerGetTabManager();
			accordion = $("#gl-left-nav").length?$("#gl-left-nav").ligerGetAccordionManager():0;
			// tree.expandAll(); //默认展开所有节点
			$("#pageloading_bg,#pageloading").hide();

			$('.l-layout-center').width( $('.l-layout-center').width() + 7 )

			//配置修改 和 门店维护冲突解决
			// $('a:contains(门店维护)').attr('href', "javascript:f_addTab('index100','门店维护','../index.php/Caryu/Store/index')")

			$(window).resize(function() {
				$('.l-layout-center').width($(window).width() - $('.l-layout-left').width())
				$('iframe').each((i, el) => {
					el.contentWindow.$('#data_list').datagrid('resize', { width: el.contentWindow.innerWidth })
				})
			})

			// 时时检测当前激活tab
			setInterval(function() {
				var selectedTabID = tab.getSelectedTabItemID()
				if (tab.getTabItemCount() && selectedTabID != 'home') {
					$('li[menuitem='+ selectedTabID +']').addClass('is-active')
					$('li[menuitem]').not('[menuitem='+ selectedTabID +']').removeClass('is-active')
				} else {
					$('li.el-menu-item').removeClass('is-active')
				}

				if (vm.isMenuOneClk && tab.getTabItemCount() === 0) {
					vm.isLoaded = false
				}
			}, 300)
		});
		//退出登录
		function logout(){
			$.messager.confirm('提示信息', '确定要退出登录吗？', function(result){
				if(result) window.location.href = '<?php echo U("Index/logout");?>';
			});
		}

		function f_heightChanged(options) {
			if (tab)
				tab.addHeight(options.diff);
			if (accordion && options.middleHeight - 24 > 0)
				accordion.setHeight(options.middleHeight - 24);
		}
		//添加Tab，可传3个参数
		function f_addTab(tabid, text, url, iconcss) {
			if (arguments.length == 4) {
				tab.addTabItem({
					tabid : tabid,
					text : text,
					url : url,
					iconcss : iconcss
				});
			} else {
				tab.addTabItem({
					tabid : tabid,
					text : text,
					url : url
				});
			}
		}

		//提示Dialog并关闭Tab
		function f_errorTab(tit, msg) {
			$.ligerDialog.open({
						isDrag : false,
						allowClose : false,
						type : 'error',
						title : tit,
						content : msg,
						buttons : [ {
							text : '确定',
							onclick : function(item, dialog, index) {
								//查找当前iframe名称
								var itemiframe = "#gl-center .l-tab-content .l-tab-content-item";
								var curriframe = "";
								$(itemiframe).each(function() {
									if ($(this).css("display") != "none") {
										curriframe = $(this).attr("tabid");
										return false;
									}
								});
								if (curriframe != "") {
									tab.removeTabItem(curriframe);
									dialog.close();
								}
							}
						} ]
					});
		}
		//退出登录
		function loginout(){
			var checkFlag = window.confirm("是否要退出登录？");
			  if(checkFlag==true){
				  window.location.href = "../index.php/Caryu/Index/logout";
			  }
		}


	</script>
<body>
	<div id="global_layout" class="layout l-layout" v-cloak v-loading.fullscreen.lock="fullscreenLoading">

		<!--头部-->
		<div position="top" id="gl-header">
			<div id="logo">
				<img src="/Public/static/packaged-assets/Caryu/img/logo.png">
				<span>后台管理系统</span>
			</div>
			<div id="user_box" title="<?php echo ($username); ?>">
				<el-dropdown
					trigger="click"
					@command="accountSelectHandle"
					@visible-change="accountMenuToggle">
					<span class="el-dropdown-link">
						<i class="el-icon-user el-icon--left"></i><?php echo ($username); ?>
						<i class="el-icon-caret-bottom el-icon--right" :class="{rotate180: isRotate}"></i>
					</span>
					<el-dropdown-menu slot="dropdown">
						<el-dropdown-item command="edit_pswd">修改密码</el-dropdown-item>
						<el-dropdown-item command="logout" :divided="true">退出</el-dropdown-item>
					</el-dropdown-menu>
				</el-dropdown>
			</div>
		</div>

		<!--左边  -->
		<div position="left" id="gl-left-nav">
			<el-menu
				theme="dark"
				default-active="2"
				:unique-opened="true"
				:collapse="isCollapse"
				class="el-menu-vertical-demo"
				@open="handleOpen"
				@close="handleClose"
			>
				 <template v-for="v-for=(menuItem, index) in menuItems">
					<el-menu-item
						v-if="menuItem.name === 'Meau/AdminPage'"
						class="el-submenu__title"
						index="1000"
						@click="menuItemClick"
						home>
						<i class="el-icon-sy"></i><span slot="title">{{ menuItem.child[0].title }}</span>
					</el-menu-item>
					<el-submenu
						v-else
						:key="index"
						:index="toString(index)">
						<template slot="title">
							<i :class="menuItem.meau_pic && ('el-icon-' + menuItem.meau_pic.replace('.png', '') )"></i>{{ menuItem.title }}
						</template>
						<template v-for="(childMenuItem, childIndex) in menuItem.child">
							<el-menu-item
								:menuitem="childMenuItem.method"
								@click="menuItemClick"
								:index="index +'-'+ childIndex">
									{{ childMenuItem.title }}
							</el-menu-item>
						</template>
					</el-submenu>
				</template>
			</el-menu>
			<svg @click="menuToggle" :class="{'is-active': isActive}" id="icon-arrow" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<path d="M12.586 27.414l-10-10c-0.781-0.781-0.781-2.047 0-2.828l10-10c0.781-0.781 2.047-0.781 2.828 0s0.781 2.047 0 2.828l-6.586 6.586h19.172c1.105 0 2 0.895 2 2s-0.895 2-2 2h-19.172l6.586 6.586c0.39 0.39 0.586 0.902 0.586 1.414s-0.195 1.024-0.586 1.414c-0.781 0.781-2.047 0.781-2.828 0z"></path>
			</svg>
		</div>

		<!--内容  -->
		<div position="center" id="gl-center" class="liger-tab">
			<div id="divHome" tabid="home" title="首页" iconcss="tab-icon-home" v-if="hasHome">
				<el-row class="gl-c-header">
					<el-col :span="12" :offset="12" :pull="1">
						<el-form :inline="true">
							<el-form-item label="城市选择：" class="gl-c-child">
								<el-dropdown trigger="click" @command="citySelectHandle">
									<el-button type="warning" class="city-select">
										{{ default_cityname }}<i class="el-icon-caret-bottom el-icon--right"></i>
									</el-button>
									<el-dropdown-menu slot="dropdown">
										<el-dropdown-item
											:key="index"
											v-for="(item, index) in cityOptions"
											:command="{value: item.city_id, label: item.city_name}">
											{{ item.city_name }}
										</el-dropdown-item>
									</el-dropdown-menu>
								</el-dropdown>
							</el-form-item>
						</el-form>
					</el-col>
				</el-row>

				<el-collapse v-model="modulesActiveNames" class="gl-c-modules">
					<el-collapse-item title="总体数据" name="data" class="m-data">
						<el-row :gutter="20">
							<el-col
								class="align-center"
								:span="3"
								:key="index"
								v-for="(item, key, index) in V_dataContent">
								<div
									class="grid-label"
									@click="f_addTab(item.tabid, item.tap_title, item.url)">
									{{ item.title }}<i class="el-icon-arrow-right"></i>
								</div>
								<div class="grid-count">{{ item.count ? item.count : 0 }}</div>
							</el-col>
						</el-row>
					</el-collapse-item>

					<el-collapse-item title="待办事项" name="backlog" class="m-backlog">
						<el-row :gutter="20">
							<el-col
								class="align-center"
								:span="8"
								:key="index"
								v-for="(item, key, index) in V_backlogContent">
								<div class="grid-title">
									{{ item.title }}
									<span v-if="key === 'pendingMoney'" @click="pendingMoneyTips" class="grid-help">?</span>
								</div>

								<el-row type="flex" justify="center">
									<el-col
										class="grid-item"
										:span="12"
										:key="childIndex"
										v-for="(childItem, childIndex) in item.child">
										<el-badge
											:value="childItem.count"
											:max="99"
											:hidden="!~~childItem.count"
											class="grid-count">
											<el-button
												plain
												type="warning"
												@click="f_addTab(childItem.tabid, childItem.tap_title, childItem.url)">
												{{ childItem.title }}
											</el-button>
										</el-badge>
									</el-col>
								</el-row>
							</el-col>
						</el-row>
					</el-collapse-item>

					<el-collapse-item title="数据分析" name="analyze" class="m-analyze">
						<el-tabs @tab-click="analyzeTableClick" class="analyze-table" active-name="1">
							<el-tab-pane label="昨天" name="1"></el-tab-pane>
							<el-tab-pane label="上周" name="7"></el-tab-pane>
							<el-tab-pane label="上月" name="30"></el-tab-pane>
							<el-row>
								<el-col
									class="align-center analyze-table-item"
									:span="3"
									:key="key"
									v-for="(item, key) in V_analyzeTableContent">
									<el-card>
										<div slot="header">{{ item.label }}</div>
										<div>{{ item.value }}</div>
									</el-card>
								</el-col>
							</el-row>
						</el-tabs>

						<el-tabs @tab-click="analyzeChartClick" type="border-card" class="analyze-chart" active-name="cameraFacility">
							<el-tab-pane label="新增设备门店" name="cameraFacility"></el-tab-pane>
							<el-tab-pane label="车险订单" name="carInsurance"></el-tab-pane>
							<el-tab-pane label="违章订单" name="carViolation"></el-tab-pane>
							<el-tab-pane label="二手车订单" name="secondHand"></el-tab-pane>
							<el-tab-pane label="车抵贷订单" name="carGuaranty"></el-tab-pane>
							<el-tab-pane label="红包数" name="redEnvelopes"></el-tab-pane>
							<el-tab-pane label="申请设备数" name="applyEquipment"></el-tab-pane>
							<el-row>
								<el-select @change="pickerShortcutSlector" v-model="latelyDateValue" placeholder="最近30天">
									<el-option
										v-for="item in latelyDateOptions"
										:key="item.value"
										:label="item.label"
										:value="item.value">
									</el-option>
								</el-select>
								<el-date-picker
									v-model="pickerValue"
									ref="picker"
									type="daterange"
									align="right"
									placeholder="选择日期范围"
									@change="pickerChangeHandle">
								</el-date-picker>
								<transition name="el-zoom-in-top">
									<el-select
										v-show="ajaxParams.type.match(/carViolation|secondHand|carGuaranty/gi)"
										v-model="ajaxParams.source"
										placeholder="请选择数据来源">
										<el-option
											v-for="item in dataSourceOptions"
											:key="item.value"
											:label="item.label"
											:value="item.value">
										</el-option>
									</el-select>
								</transition>
							</el-row>
							<el-row>
								<div id="analyze-chart-main"></div>
							</el-row>
						</el-tabs>
					</el-collapse-item>
				</el-collapse>
			</div>
		</div>

		<!--欢迎页  -->
		<transition name="el-fade-in" @before-enter="showWelcome" @after-leave="hideWelcome">
			<div position="center" id="welcome" v-show="!isLoaded">
				<ul>
					<li>
						<h2>欢迎</h2>
						<h3>使用驾遇后台管理系统</h3>
					</li>
					<li><img src="/Public/static/packaged-assets/Caryu/img/loading-icon.png"></li>
				</ul>
			</div>
		</transition>

		<el-dialog title="修改密码" :visible.sync="dialogFormVisible" size="tiny" @close="dialogCloseHandle">
			<el-form :model="form">
				<el-form-item label="原密码" :label-width="formLabelWidth">
					<el-input v-model="form.old_pswd" placeholder="请输入原密码" auto-complete="off"></el-input>
				</el-form-item>
				<el-form-item label="新密码" :label-width="formLabelWidth">
					<el-input v-model="form.new_pswd" placeholder="请输入新密码" auto-complete="off"></el-input>
				</el-form-item>
				<div class="pswd-tooltip">请输入由字母,数字任意组合的6-18位密码</div>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button type="warning" plain @click="dialogFormVisible = false">取 消</el-button>
				<el-button type="warning" @click="changePswdOk">确 定</el-button>
			</div>
		</el-dialog>

		<div class="l-layout-lock"></div>
		<div style="display: block; top: 50px; width: 1366px;" class="l-layout-drophandle-top"></div>
		<div class="l-layout-dragging-xline"></div>
		<div class="l-layout-dragging-yline"></div>
		<div class="l-layout-collapse-left" style="display: none;">
			<div class="l-layout-collapse-left-toggle"></div>
		</div>
		<div class="l-layout-collapse-right" style="display: none;">
			<div class="l-layout-collapse-right-toggle"></div>
		</div>
	</div>
</body>

</html>