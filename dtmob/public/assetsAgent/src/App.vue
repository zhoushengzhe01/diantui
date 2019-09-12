<template>
<div id="app">
    <el-container style="height:100%">
        <el-container>
            <el-aside width="180px">
				<div class="logo">
					<img src="/website/images/logo.png"/>
				</div>

				<div class="money_box">
					余额：{{agent.money}} 元
					<br/>
					<a v-bind:href="'http://wpa.qq.com/msgrd?v=3&uin='+agent.busine.qq+'&site=qq&menu=yes'">客服 {{agent.busine.stagename}} <i class="fa fa-qq" aria-hidden="true"></i></a>
				</div>
				<br/>

				<el-menu
			      	background-color="#545c64"
			      	text-color="#fff"
			      	:default-active="group.page"
			      	:router="true"
			    	>
			      	<el-menu-item index="/agent">
			        	<i class="el-icon-menu"></i>
			        	<span slot="title">管理首页</span>
			      	</el-menu-item>
					 
					<el-menu-item index="/agent/lowers">
			        	<i class="el-icon-picture-outline"></i>
			        	<span slot="title">下线列表</span>
			      	</el-menu-item>
					  
					<el-menu-item index="/agent/earnings">
			        	<i class="el-icon-view"></i>
			        	<span slot="title">收益报表</span>
					</el-menu-item>
					  
					<el-menu-item index="/agent/moneys">
			        	<i class="el-icon-view"></i>
			        	<span slot="title">提现记录</span>
					</el-menu-item>
					  
					<el-menu-item index="/agent/user">
			        	<i class="el-icon-goods"></i>
			        	<span slot="title">个人信息</span>
					</el-menu-item>
					  
					<el-menu-item index="/agent/logs">
			        	<i class="el-icon-edit-outline"></i>
			        	<span slot="title">登录日志</span>
					</el-menu-item>

			      	<el-menu-item index="/agent/link">
			        	<i class="el-icon-date"></i>
			        	<span slot="title">推广链接</span>
					</el-menu-item>
				</el-menu>

			</el-aside>
            <el-container>
				<el-header>
					<router-link class="username" to="/agent/user">{{agent.username}} <i aria-hidden="true" class="fa fa-user-o"></i></router-link><a href="javascript:void(0)" class="loginout" @click="Logout()">退出登录</a>
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
			agent: Group.agent,		
		};
	},
	created: function () {

	},
	methods:{
		Logout: function()
		{
			var Th = this;

            Th.loading = true;

            Th.$api.put('agent/logout.json', {}, function(data){
                
                Th.message('success', '推出成功');
				window.location.href = '/agent/login';
				
            }, function(type, message){ Th.message(type, message) });
		},

		message: function(type, message){
            this.$message({ showClose: true, message: message, type: type });
            this.loading = false;
        }
	},
}
</script>