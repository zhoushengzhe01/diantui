<template>
<div id="app">
    <el-container style="height:100%">
        <el-container>
            <el-aside width="180px">
				<div class="logo">
					<img src="/website/images/logo.png"/>
				</div>

				<div class="money_box">
					余额：{{webmaster.money}} 元
					<br/>
					<a target="_blank" v-bind:href="'http://wpa.qq.com/msgrd?v=3&uin='+webmaster.service.qq+'&site=qq&menu=yes'">客服 {{webmaster.service.stagename}} <i class="fa fa-qq" aria-hidden="true"></i></a>
				</div>
				<br/>

				<el-menu
			      	background-color="#545c64"
			      	text-color="#fff"
			      	:default-active="group.page"
			      	:router="true"
			    	>
			      	<el-menu-item index="/webmaster">
			        	<i class="el-icon-menu"></i>
			        	<span slot="title">管理首页</span>
			      	</el-menu-item>

			     	<el-menu-item index="/webmaster/websites">
			        	<i class="el-icon-picture-outline"></i>
			        	<span slot="title">网站管理</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/adtype">
			        	<i class="el-icon-view"></i>
			        	<span slot="title">推荐广告</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/myads">
			        	<i class="el-icon-goods"></i>
			        	<span slot="title">我的广告</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/earnings">
			        	<i class="el-icon-edit-outline"></i>
			        	<span slot="title">佣金报表</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/rewards">
			        	<i class="el-icon-date"></i>
			        	<span slot="title">奖励管理</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/expends">
			        	<i class="el-icon-document"></i>
			        	<span slot="title">财务结算</span>
					</el-menu-item>

			      	<el-menu-item index="/webmaster/user">
			        	<i class="el-icon-message"></i>
			        	<span slot="title">个人信息</span>
			      	</el-menu-item>

			      	<el-menu-item index="/webmaster/lower">
			        	<i class="el-icon-view"></i>
			        	<span slot="title">下线链接</span>
			      	</el-menu-item>

			    </el-menu>

			</el-aside>
            <el-container>
				<el-header>
					<router-link class="username" to="/webmaster/user">{{webmaster.username}} <i aria-hidden="true" class="fa fa-user-o"></i></router-link><a href="javascript:void(0)" class="loginout" @click="Logout()">退出登录</a>
				</el-header>
            	<el-main>
					<router-view @message="message"></router-view>
				</el-main>
        	</el-container>
    	</el-container>
    </el-container>
</div>
</template>

<script>
export default {
	name: 'app',
	data: function () {	
		return {
			isActive: true,
			group: Group,
			webmaster: Group.webmaster,		
		};
	},
	created: function () {

	},
	methods:{
		Logout: function()
		{
			var Th = this;

            Th.loading = true;

            Th.$api.put('webmaster/logout.json', {}, function(data){
                
                Th.message('success', '推出成功');

                window.location.href = '/';

            }, function(type, message){ Th.message(type, message) });
		},

		message: function(type, message){
            this.$message({ showClose: true, message: message, type: type });
            this.loading = false;
        }
	},
}
</script>